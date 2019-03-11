<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\NombreNomina;
use App\TipoNomina;
use PDF;




class CfdiController extends Controller
{   


    public function __construct()
    {
        
        $this->middleware('auth');
        $this->middleware('verified');
            
    }

    public function downloadXml($id)
    {
    
            $cfdi = \DB::table('cfdis') 
                ->select('name','nomina', 'qna', 'year', 'tipo', 'xml') 
                ->where('id',$id) 
                ->first();

            $file = $cfdi->name.'-'.
                    $cfdi->nomina.'-'.
                    $cfdi->tipo.'-'.
                    $cfdi->qna.'-'.
                    $cfdi->year;

            return response($cfdi->xml, 200, [
                'Content-Type' => 'application/xml',
                'Content-Disposition' => 'attachment; filename="'.$file.'.xml"',
                'Content-Description' => 'File Transfer',
                'Content-Transfer-Encoding' => 'binary',
                'Content-length' => strlen($cfdi->xml)
            ]);  
    }

    
    
    public function uploadCfdis(Request $request)
    {
        
        $documento_xml = file_get_contents($request->file);   
              
        $timbre = new \SimpleXMLElement($documento_xml);

        $ns = $timbre->getNamespaces(true);
        
        foreach ($timbre->xpath('//cfdi:Comprobante') as $Comprobante){
            $folio = $Comprobante['Folio'];
            $serie = $Comprobante['Serie'];                                 
        }   

        foreach ($timbre->xpath('//cfdi:Comprobante//cfdi:Receptor') as $Receptor){
            $rfc = $Receptor['Rfc'];
            $nombre = $Receptor['Nombre'];
        }
                        
        $timbre->registerXPathNamespace('c', $ns['cfdi']);  
        $timbre->registerXPathNamespace('t', $ns['nomina12']);

        foreach ($timbre->xpath('//t:Nomina') as $tfd) {
            $FechaInicialPago = $tfd['FechaInicialPago'];
            $FechaFinalPago = $tfd['FechaFinalPago'];
            $FechaPago = $tfd['FechaPago'];
        }

       
        if (array_key_exists('tfd', $ns)) {
            $timbre->registerXPathNamespace('c', $ns['cfdi']);
            $timbre->registerXPathNamespace('t', $ns['tfd']);
                foreach ($timbre->xpath('//t:TimbreFiscalDigital') as $TimbreFiscalDigital) {
                    $FechaTimbrado = $TimbreFiscalDigital['FechaTimbrado'];             
                    $UUID = $TimbreFiscalDigital['UUID'];
                    $FechaTimbrado = substr($FechaTimbrado, 0, 10);
        }
        }else {
            $UUID = null;
        }

        $nomina = strtok($serie, '-');
        $substring = substr($serie, strpos($serie, "-") + 1);
        $tipo = strtok($substring, '-');
        $substring = substr($substring, strpos($substring, "-") + 1);
        $qna = strtok($substring, '-');
        $substring = substr($substring, strpos($substring, "-") + 1);
        $year = strtok($substring, '-');

        $cfdi = new \App\Cfdi;
        $cfdi->name = $rfc;
        $cfdi->qna = $qna;//$request->input('qna_nomina');//
        $cfdi->year = $year;//$request->input('anio_nomina');//
        $cfdi->tipo = $tipo;//$request->input('tipo_nomina');//
        $cfdi->cr = explode("/",(explode("/", $request->path, 2)[1]),2)[0];
        $cfdi->nomina = $nomina;//$request->input('nombre_nomina');//
        $cfdi->xml = $documento_xml;
        $cfdi->save();
        
    }

    public function index()
    {

        if (Gate::denies('isAdmin')) {
            abort(403,"Lo siento, Usted no tiene autorizado el acceso a este recurso. Este incidente será reportado a el administrador del sistema.");
        }

        $data = [
            'nominas'  => NombreNomina::all(),
            'tipos'  => TipoNomina::all()
        ];

        return view('cfdis.indexCfdis')->with($data);
        
    }

    public function downloadPdf($id){            
        
        $cfdi = \DB::select("SELECT cr,
            extractValue(xml,'//cfdi:Comprobante/cfdi:Receptor/@Rfc') as rfcReceptor,
            extractValue(xml,'//cfdi:Comprobante/cfdi:Receptor/@Nombre') as nombreReceptor,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/@FechaPago') as FechaPago,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Receptor/@Puesto') as puesto,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Receptor/@Departamento') as departamento,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Receptor/@Curp') as curp,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Emisor/@RegistroPatronal') as registroPatronal,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/@TipoNomina') as tipoNomina,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/@NumDiasPagados') as numDiasPagados,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Receptor/@PeriodicidadPago') as periodicidadPago,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Receptor/@TipoContrato') as tipoContrato,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Receptor/@TipoRegimen') as tipoRegimen,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Receptor/@RiesgoPuesto') as riesgoPuesto,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Receptor/@NumEmpleado') as numEmpleado,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Receptor/@NumSeguridadSocial') as numSeguridadSocial,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Receptor/@TipoJornada') as tipoJornada,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/@FechaPago') as fechaPago,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/@FechaInicialPago') as fechaInicialPago,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/@FechaFinalPago') as fechaFinalPago,
            extractValue(xml,'//cfdi:Comprobante/@Descuento') as totalDeducciones,
            extractValue(xml,'//cfdi:Comprobante/@Total') as total,
            extractValue(xml,'//cfdi:Comprobante/@SubTotal') as subTotal,
            extractValue(xml,'//cfdi:Complemento/tfd:TimbreFiscalDigital/@UUID') as UUID,
            extractValue(xml,'//cfdi:Complemento/tfd:TimbreFiscalDigital/@Version') as versionCertificadoSAT,
            extractValue(xml,'//cfdi:Complemento/tfd:TimbreFiscalDigital/@SelloSAT') as selloSAT,
            extractValue(xml,'//cfdi:Complemento/tfd:TimbreFiscalDigital/@SelloCFD') as selloCFD,
            extractValue(xml,'//cfdi:Complemento/tfd:TimbreFiscalDigital/@NoCertificadoSAT') as noCertificadoSAT,
            extractValue(xml,'//cfdi:Complemento/tfd:TimbreFiscalDigital/@FechaTimbrado') as fechaTimbrado,
            extractValue(xml,'//cfdi:Comprobante/@NoCertificado') as noCertificado,
            extractValue(xml,'//cfdi:Comprobante/@Serie') as serie,
            extractValue(xml,'//cfdi:Comprobante/@Fecha') as fecha,
            extractValue(xml,'//cfdi:Comprobante/@Folio') as folio,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Percepciones/nomina12:Percepcion/@Clave') as clavesPercepcion,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Percepciones/nomina12:Percepcion/@ImporteExento') as importesExentosPercepcion,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Percepciones/nomina12:Percepcion/@ImporteGravado') as importesGravadosPercepcion,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Deducciones/nomina12:Deduccion/@Clave') as clavesDeduccion,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:Deducciones/nomina12:Deduccion/@Importe') as importesDeduccion,

            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:OtrosPagos/nomina12:OtroPago/@Clave') as clavesOtroPago,
            extractValue(xml,'//cfdi:Complemento/nomina12:Nomina/nomina12:OtrosPagos/nomina12:OtroPago/@Importe') as importesOtroPago

            FROM rhsalud.cfdis WHERE id = ?",[$id]);


        $nomina['tipoNomina'] = $cfdi[0]->tipoNomina;
        $nomina['numDiasPagados'] = $cfdi[0]->numDiasPagados;
        $nomina['periodicidadPago'] = $cfdi[0]->periodicidadPago;
        $nomina['tipoContrato'] = $cfdi[0]->tipoContrato;
        $nomina['tipoRegimen'] = $cfdi[0]->tipoRegimen;
        $nomina['riesgoPuesto'] = $cfdi[0]->riesgoPuesto;
        $nomina['fechaPago'] = $cfdi[0]->fechaPago;
        $nomina['fechaInicialPago'] = $cfdi[0]->fechaInicialPago;
        $nomina['fechaFinalPago'] = $cfdi[0]->fechaFinalPago;
        $nomina['numEmpleado'] = $cfdi[0]->numEmpleado;
        $nomina['numSeguridadSocial'] = $cfdi[0]->numSeguridadSocial;
        $nomina['tipoJornada'] = $cfdi[0]->tipoJornada;
        $nomina['registroPatronal'] = $cfdi[0]->registroPatronal;

        $nomina['versionCertificadoSAT'] = $cfdi[0]->versionCertificadoSAT;
        $nomina['selloSAT'] = $cfdi[0]->selloSAT;
        $nomina['selloCFD'] = $cfdi[0]->selloCFD;
        $nomina['noCertificadoSAT'] = $cfdi[0]->noCertificadoSAT;
        $nomina['noCertificado'] = $cfdi[0]->noCertificado;
        $nomina['fechaTimbrado'] = $cfdi[0]->fechaTimbrado;
        
        $nomina['clavePresupestal'] = "";
        $nomina['clue'] = $cfdi[0]->cr;
        $nomina['hrs'] = "8";
        

        $nomina['rfcReceptor'] = $cfdi[0]->rfcReceptor;
        $nomina['UUID'] = $cfdi[0]->UUID;
        $nomina['nombreReceptor'] = $cfdi[0]->nombreReceptor;
        $nomina['puesto'] = $cfdi[0]->puesto;
        $nomina['departamento'] = $cfdi[0]->departamento;
        $nomina['curp'] = $cfdi[0]->curp;
        $nomina['serie'] = $cfdi[0]->serie;
        $nomina['folio'] = $cfdi[0]->folio;
        $nomina['fecha'] = $cfdi[0]->fecha;
            $nombre_nomina = strtok($nomina['serie'], '-');
                $substring = substr($nomina['serie'], strpos($nomina['serie'], "-") + 1);
            $tipo = strtok($substring, '-');
                $substring = substr($substring, strpos($substring, "-") + 1);
            $qna = strtok($substring, '-');
                $substring = substr($substring, strpos($substring, "-") + 1);
            $year = strtok($substring, '-');
        $nomina['file'] = $nomina['rfcReceptor'].'-'.$nomina['serie'].'.pdf';
        $nomina['quincena'] = $qna;
        $nomina['anio'] = $year;
        $nomina['totalDeducciones'] = $cfdi[0]->totalDeducciones;
        $nomina['total'] = $cfdi[0]->total;
        $nomina['subTotal'] = $cfdi[0]->subTotal;

        $nomina['percepciones'] = array();
        $nomina['deducciones'] = array();
        $nomina['otrosPagos'] = array();

        
        $clavesPercepcion = explode(" ",  $cfdi[0]->clavesPercepcion);
        $importesExentosPercepcion = explode(" ",  $cfdi[0]->importesExentosPercepcion);
        $importesGravadosPercepcion = explode(" ",  $cfdi[0]->importesGravadosPercepcion);
        for ($i=0; $i < count($clavesPercepcion); $i++) { 
            array_push($nomina['percepciones'], (object) [
            'Clave' => $clavesPercepcion[$i],
            'Concepto' => "Concepto No: ".$i,
            'Importe' => $importesExentosPercepcion[$i]+$importesGravadosPercepcion[$i]
            ]);
        }

        $clavesDeduccion = explode(" ",  $cfdi[0]->clavesDeduccion);
        $importesDeduccion = explode(" ",  $cfdi[0]->importesDeduccion);
        for ($i=0; $i < count($clavesDeduccion); $i++) { 
            if($clavesDeduccion[$i] != "")
               array_push($nomina['deducciones'], (object) [
                    'Clave' => $clavesDeduccion[$i],
                    'Concepto' => "Concepto No: ".$i,
                    'Importe' => $importesDeduccion[$i]
                ]); 
        }

        $clavesOtroPago = explode(" ",  $cfdi[0]->clavesOtroPago);
        $importesOtroPago = explode(" ",  $cfdi[0]->importesOtroPago);
        for ($i=0; $i < count($clavesOtroPago); $i++) {
            if($clavesOtroPago[$i] != "")
            array_push($nomina['otrosPagos'], (object) [
                'Clave' => $clavesOtroPago[$i],
                'Concepto' => "Concepto Otro Pago: ".$i,
                'Importe' => $importesOtroPago
            ]);
        }

        $this->formatoNuevo($nomina);
    }


    function formatoNuevo($nomina)
    {

                setlocale(LC_MONETARY, 'en_US.UT,F-8');
                PDF::reset();
                PDF::SetTitle('CFDI');
                PDF::setPrintHeader(false);
                PDF::setFooterCallback(function($pdf){
                    $pdf->SetY(-15);          
                    $pdf->SetFont('helvetica', 'I', 7);  
                    $pdf->Image('img/PlecaTejidos.jpg', 9, 275, 192, 5, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
                    $pdf->Cell(0, 0, 'Este documento es una representación impresa de un CFDI, Versión del comprobante: 3.3, Versión del complemento: 1.2', 0, false, 'R', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell(0, 10, 'www.salud.guerrero.gob.mx', 0, false, 'R', 0, '', 0, false, 'T', 'M');
                });          
                PDF::SetFont('helvetica', 'B', 18);         
                PDF::AddPage(); 
                PDF::Image('img/SecSalud.jpg', 30, 10, 50, 20, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
                PDF::Cell(287, 20,'RECIBO DE NÓMINA', 0, 1, 'C', 0, '', 0);

                PDF::SetFont('helvetica', 'BI', 8);
                //PDF::Cell(0, 0, '"MÉXICO UNIDO RUMBO A LA ELIMINACIÓN DE LA TUBERCULOSIS"',0, 0, 'R', 0);
                PDF::Cell(0, 0, '',0, 0, 'R', 0);
                PDF::Ln(3);
                //PDF::Cell(0, 0, '"24 DE MARZO DÍA MUNDIAL DE LA  LUCHA CONTRA LA TUBERCULOSIS"',0, 0, 'R', 0);
                PDF::Cell(0, 0, '',0, 0, 'R', 0);
                PDF::SetFont('helvetica', '', 7);
                PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                PDF::RoundedRect(9, 38, 192, 22, 3.50, '1111', 'DF',array(),array(255, 255, 255));
                
                PDF::Ln(6);             
                PDF::Cell(40, 3, 'RFC Empleador: SES870401TX8',0, 0, 'L', 0);
                PDF::Cell(50, 3, '',0, 0, 'L', 0);
                PDF::Cell(13, 3, '',0, 0, 'L', 0);
                PDF::Cell(40, 3, 'RFC Empleado: '.$nomina['rfcReceptor'],0, 0, 'L', 0);
                PDF::Cell(50, 3, '',0, 0, 'L', 0);
                PDF::Ln();
                PDF::SetFont('helvetica', 'B', 7);
                PDF::Cell(40, 3, 'SERVICIOS ESTATALES DE SALUD',0, 0, 'L', 0);              
                PDF::SetFont('helvetica', '', 7);
                PDF::Cell(50, 3, '',0, 0, 'L', 0);
                PDF::Cell(13, 3, '',0, 0, 'L', 0);
                PDF::SetFont('helvetica', 'B', 7);
                PDF::Cell(40, 3, $nomina['nombreReceptor'],0, 0, 'L', 0);
                PDF::SetFont('helvetica', '', 7);
                PDF::Cell(50, 3, '',0, 0, 'L', 0);
                PDF::Ln();
                PDF::Cell(40, 3, 'AV. RUFFO FIGUEROA No. 6',0, 0, 'L', 0);
                PDF::Cell(50, 3, '',0, 0, 'L', 0);
                PDF::Cell(13, 3, '',0, 0, 'L', 0);
                PDF::Cell(30, 3, $nomina['clavePresupestal'],0, 0, 'L', 0);
                PDF::Cell(50, 3, '',0, 0, 'L', 0);
                PDF::Ln();
                PDF::Cell(40, 3, 'COL. BUROCRATAS, C.P. 39090',0, 0, 'L', 0);
                PDF::Cell(50, 3, '',0, 0, 'L', 0);
                PDF::Cell(13, 3, '',0, 0, 'L', 0);
                PDF::Cell(30, 3, $nomina['puesto'],0, 0, 'L', 0);
                PDF::Cell(50, 3, '',0, 0, 'L', 0);
                PDF::Ln();
                PDF::Cell(40, 3, 'CHILPANCINGO DE LOS BRAVO. GUERRERO',0, 0, 'L', 0);
                PDF::Cell(50, 3, '',0, 0, 'L', 0);
                PDF::Cell(13, 3, '',0, 0, 'L', 0);
                PDF::Cell(30, 3, $nomina['clue'].' '.$nomina['departamento'],0, 0, 'L', 0);
                PDF::Cell(50, 3, '',0, 0, 'L', 0);
                PDF::Cell(191, 3, '',0, 0, 'C', 0);
                PDF::Ln();
                PDF::Cell(40, 3, 'NOMINA: '.$nomina['serie'],0, 0, 'L', 0);
                PDF::Cell(50, 3, '',0, 0, 'L', 0);
                PDF::Cell(13, 3, '',0, 0, 'L', 0);
                PDF::Cell(30, 3, 'HRS: '.$nomina['hrs'].'  CURP: '.$nomina['curp'],0, 0, 'L', 0);
                PDF::Cell(50, 3, '',0, 0, 'L', 0);
                PDF::Cell(191, 3, '',0, 0, 'C', 0);
                PDF::Ln(8);
                
                $html = '
                <font size="7" face="Courier New">
                <table width="100%" cellpadding="2">
                    <tr><th colspan="6" style="font-weight:bold;text-align:center; background-color:#f2f2f2;">DESGLOSE DE PERCEPCIONES Y DESCUENTOS DE LA QUINCENA '.$nomina['quincena'].' DEL '.$nomina['anio'].'</th></tr>
                  <tr>
                    <th colspan="3" style="font-weight:bold;text-align:center; border: 0.5px solid black;">Percepciones</th>
                    <th colspan="3" style="font-weight:bold;text-align:center; border: 0.5px solid black;">Descuentos</th>
                  </tr>
                  <tr style="font-weight:bold;text-align:center">
                    <td width="5%" style="font-weight:bold;text-align:center; border: 0.5px solid black;">Clave</td>
                    <td width="35%" style="font-weight:bold;text-align:center; border: 0.5px solid black;">Concepto</td>
                    <td width="10%" style="font-weight:bold;text-align:center; border: 0.5px solid black;">Importe</td>
                    <td width="5%" style="font-weight:bold;text-align:center; border: 0.5px solid black;">Clave</td>
                    <td width="35%" style="font-weight:bold;text-align:center; border: 0.5px solid black;">Concepto</td>
                    <td width="10%" style="font-weight:bold;text-align:center; border: 0.5px solid black;">Importe</td>
                  </tr>';

                   $percepciones = $nomina['percepciones'];
                   $otrospagos = $nomina['otrosPagos'];
                   $deducciones = $nomina['deducciones'];
                   $percepciontable = '';


                   
                   $x = 0;
                       if( count($percepciones) >= count($deducciones)){
                           foreach ($percepciones as $percepcion) {
                               if($x < count($deducciones)){
                                   $percepciontable = $percepciontable .'<tr><td style="border: 0.5px solid black;">'.substr($percepcion->Clave,1,2).'</td>';
                                   $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;">'.$percepcion->Concepto.'</td>';
                                   $percepciontable = $percepciontable .'<td style="border: 0.5px solid black; text-align:right">'.money_format('%.2n',$percepcion->Importe).'</td>';
                                   $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;">'.substr($deducciones[$x]->Clave,1,2).'</td>';
                                   $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;">'.$deducciones[$x]->Concepto.'</td>';
                                   $percepciontable = $percepciontable .'<td style="border: 0.5px solid black; text-align:right">'.money_format('%.2n',$deducciones[$x]->Importe).'</td></tr>';
                                   $x++;
                               }else{
                                   $percepciontable = $percepciontable .'<tr><td style="border: 0.5px solid black;">'.substr($percepcion->Clave,1,2).'</td>';   
                                   $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;">'.$percepcion->Concepto.'</td>';
                                   $percepciontable = $percepciontable .'<td style="border: 0.5px solid black; text-align:right">'.money_format('%.2n',$percepcion->Importe).'</td>';
                                   $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;"></td>';
                                   $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;"></td>';
                                   $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;"></td></tr>';
                               }
                           }
                        }else {
                           foreach ($deducciones as $deduccion) {
                                if($x < count($percepciones)){
                                    $percepciontable = $percepciontable .'<tr><td style="border: 0.5px solid black;">'.substr($percepciones[$x]->Clave,1,2).'</td>';
                                    $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;">'.$percepciones[$x]->Concepto.'</td>';
                                    $percepciontable = $percepciontable .'<td style="border: 0.5px solid black; text-align:right">'.money_format('%.2n',$percepciones[$x]->Importe).'</td>';
                                    $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;">'.substr($deduccion->Clave,1,2).'</td>';
                                    $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;">'.$deduccion->Concepto.'</td>';
                                    $percepciontable = $percepciontable .'<td style="border: 0.5px solid black; text-align:right">'.money_format('%.2n',$deduccion->Importe).'</td></tr>';
                                $x++;
                                }else{
                                    $percepciontable = $percepciontable .'<tr><td style="border: 0.5px solid black;"></td>';
                                    $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;"></td>';
                                    $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;"></td>';
                                    $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;">'.substr($deduccion->Clave,1,2).'</td>';
                                    $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;">'.$deduccion->Concepto.'</td>';
                                    $percepciontable = $percepciontable .'<td style="border: 0.5px solid black; text-align:right">'.money_format('%.2n',$deduccion->Importe).'</td></tr>';
                                }
                           }
                        }

                        foreach ($otrospagos as $otropago) {
                            $percepciontable = $percepciontable .'<tr><td style="border: 0.5px solid black;">'.substr($otropago->Clave,1,2).'</td>';
                            $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;">'.$otropago->Concepto.'</td>';
                            $percepciontable = $percepciontable .'<td style="border: 0.5px solid black; text-align:right">'.money_format('%.2n',$otropago->Importe).'</td>';
                            $percepciontable = $percepciontable .'<td style="border: 0.5px solid black;"></td>';
                            $percepciontable = $percepciontable .'<td style="border: 0.5px solid black; text-align:right"></td></tr>';
                        }

                    
                      $html = $html . $percepciontable . '
                      <tr style="background-color:#f2f2f2;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="font-weight:bold; text-align:right">Total Percepciones:</td>
                        <td style="font-weight:bold; border: 0.5px solid black; text-align:right">'.money_format('%.2n',$nomina['subTotal']).'</td>
                      </tr>
                      <tr style="background-color:#f2f2f2;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style=" font-weight:bold; text-align:right">Total Descuentos:</td>
                        <td style=" font-weight:bold; border: 0.5px solid black; text-align:right">'.money_format('%.2n',$nomina['totalDeducciones']).'</td>
                      </tr>                       
                      <tr style="background-color:#f2f2f2;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="font-weight:bold; text-align:right">Neto a pagar</td>
                        <td style="font-weight:bold; border: 0.5px solid black; text-align:right">'.money_format('%.2n',$nomina['total']).'</td>
                      </tr>
                    </table>
                    </font>
                    ';


               PDF::writeHTML($html, true, false, false, false, '');        
               PDF::Ln(2);
              
                $tipoNomina = ($nomina['tipoNomina'] == 'O')?' - Nómina Ordinaria':' - Nómina Extraordinaria';
                $periodicidadPago = ($nomina['periodicidadPago'] == '04')?' - Quincenal':' - Otra Periodicidad';
                $numDiasPagados = ($nomina['numDiasPagados'] == '0.001')?'0 - Días':'15 - Días';
                $descTipoContrato = ($nomina['tipoContrato'] == '01')?' - Por tiempo indeterminado':' - Otro contrato';
                $descTipoRegimen = ($nomina['tipoRegimen'] == '02')?' - Sueldos':' - Otro Regimen';
                $descriesgoPuesto = ($nomina['riesgoPuesto'] == '3')?' - Clase III':'';

                      $html ='
            <p>Se puso a mi disposición el archivo XML correspondiente y recibí de la Institución la cantidad neta a que este documento se refiere estando conforme con las percepciones y deducciones que en él aparecen especificados.<br><br><br><br><br>___________________________________<br>Firma del Empleado</p>

            <table style="width:100%; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95;border:1px red solid;">

            <tr>
                                <th colspan="4" style="text-align:center">DATOS EXCLUSIVOS DEL SAT</th>
            </tr>

            <tr>
                                <th width="20%">Folio Fiscal UUID:</th>
                                <th width="30%">'.$nomina['UUID'].'</th>
                                <th width="20%">Registro Patronal:</th>
                                <th width="30%">'.$nomina['registroPatronal'].'</th>
            </tr>
            <tr>
                                <th>Certificado SAT:</th>
                                <th>'.$nomina['noCertificadoSAT'].'</th>
                                <th>Riesgo de puesto:</th>
                                <th>'.$nomina['riesgoPuesto'].$descriesgoPuesto.'</th>
            </tr>
            <tr>
                                <th>Certificado del emisor::</th>
                                <th>'.$nomina['noCertificado'].'</th>
                                <th>Tipo de régimen:</th>
                                <th>'.$nomina['tipoRegimen'].$descTipoRegimen.'</th>
            </tr>
            <tr>
                                <th>Fecha y hora de certificación:</th>
                                <th>'.$nomina['fechaTimbrado'].'</th>
                                <th>Tipo de contrato:</th>
                                <th>'.$nomina['tipoContrato'].$descTipoContrato.'</th>
            </tr>
            <tr>
                                <th>Régimen fiscal:</th>
                                <th>Personas Morales con Fines no Lucrativos</th>
                                <th>Tipo de jornada:</th>
                                <th>'.$nomina['tipoJornada'].' - Diurna</th>
            </tr>
            <tr>
                                <th>Expedición:</th>
                                <th>Chilpancingo de los Bravo. Guerrero 39090</th>
                                <th>Fecha de pago:</th>
                                <th>'.$nomina['fechaPago'].'</th>
            </tr>
            <tr>
                                <th>Tipo de comprobante:</th>
                                <th>Pago de nómina</th>
                                <th>Fecha inicial de pago:</th>
                                <th>'.$nomina['fechaInicialPago'].'</th>
            </tr>
            <tr>
                                <th>Folio/Serie:</th>
                                <th>'.$nomina['folio'].'/'.$nomina['serie'].'</th>
                                <th>Fecha final de pago:</th>
                                <th>'.$nomina['fechaFinalPago'].'</th>
            </tr>
            <tr>
                                <th>Fecha y hora de emisión:</th>
                                <th>'.$nomina['fecha'].'</th>
                                <th>Periodo de pago:</th>
                                <th>'.$nomina['periodicidadPago'].$periodicidadPago.'</th>
            </tr>
            <tr>
                                <th>Forma de pago:</th>
                                <th>En una sola exhibición</th>
                                <th>Número de días pagados:</th>
                                <th>'.$numDiasPagados.'</th>
            </tr>
            <tr>
                                <th>Tipo de Nomina:</th>
                                <th>'.$nomina['tipoNomina'].$tipoNomina.'</th>
                                <th>Inicio de la relación laboral:</th>
                                <th></th>
            </tr>
            <tr>
                                <th>Numero de Empleado:</th>
                                <th>'.$nomina['numEmpleado'].'</th>
                                <th>Numero de Seguridad Social:</th>
                                <th>'.$nomina['numSeguridadSocial'].'</th>
            </tr>

            <tr>
                                <th colspan="4" style="text-align:center">Datos obligatorios por disposiciones fiscales.</th>
            </tr>                   
            </table>

                              <table style="width:100%">
                              <tr>
                                <th></th>
                                <th></th>
                              </tr>
                              <tr>
                                <td width="75%"><b>Cadena Original del Complemento de Certificación Digital del SAT</b></td>
                                <td width="25%"></td>
                              </tr>
                              <tr>
                                <td><small>||'.$nomina['versionCertificadoSAT'].'|'.$nomina['UUID'].'|'.$nomina['fechaTimbrado'].'|'.$nomina['selloSAT'].'|'.$nomina['noCertificadoSAT'].'||</small></td>
                                <td></td>
                              </tr>
                              <tr>
                                <td><b>Sello Digital del CFDI</b></td>
                                <td></td>
                              </tr>
                              <tr>
                                <td><small>'.$nomina['selloSAT'].'</small></td>
                                <td></td>
                              </tr>
                              <tr>
                                <td><b>Sello Digital del SAT</b></td>
                                <td></td>
                              </tr>
                              <tr>
                                <td><small>'.$nomina['selloCFD'].'</small></td>
                                <td></td>
                              </tr>
                            </table>';
                    PDF::writeHTML($html, true, false, false, false, '');
                    $style = array('border' => 0,'vpadding' => 'auto','hpadding' => 'auto','fgcolor' => array(0,0,0),
                                   'bgcolor' => false,'module_width' => 1,'module_height' => 1);
                    PDF::write2DBarcode('?re=SES870401TX8&rr='.$nomina['rfcReceptor'].'&tt='.$nomina['total'].'&id='.$nomina['UUID'], 'QRCODE,L', 160, 240   , 35, 35, $style, 'N');
                    
                    PDF::Output($nomina['file'],'D'); 
    }

    function num2letras($num, $fem = false, $dec = true)
    { 
        if($num == '0' || '') return "cero pesos 00/100 M.N.";
           $matuni[2]  = "dos"; 
           $matuni[3]  = "tres"; 
           $matuni[4]  = "cuatro"; 
           $matuni[5]  = "cinco"; 
           $matuni[6]  = "seis"; 
           $matuni[7]  = "siete"; 
           $matuni[8]  = "ocho"; 
           $matuni[9]  = "nueve"; 
           $matuni[10] = "diez"; 
           $matuni[11] = "once"; 
           $matuni[12] = "doce"; 
           $matuni[13] = "trece"; 
           $matuni[14] = "catorce"; 
           $matuni[15] = "quince"; 
           $matuni[16] = "dieciseis"; 
           $matuni[17] = "diecisiete"; 
           $matuni[18] = "dieciocho"; 
           $matuni[19] = "diecinueve"; 
           $matuni[20] = "veinte"; 
           $matunisub[2] = "dos"; 
           $matunisub[3] = "tres"; 
           $matunisub[4] = "cuatro"; 
           $matunisub[5] = "quin"; 
           $matunisub[6] = "seis"; 
           $matunisub[7] = "sete"; 
           $matunisub[8] = "ocho"; 
           $matunisub[9] = "nove";
           $matdec[2] = "veint"; 
           $matdec[3] = "treinta"; 
           $matdec[4] = "cuarenta"; 
           $matdec[5] = "cincuenta"; 
           $matdec[6] = "sesenta"; 
           $matdec[7] = "setenta"; 
           $matdec[8] = "ochenta"; 
           $matdec[9] = "noventa"; 
           $matsub[3]  = 'mill'; 
           $matsub[5]  = 'bill'; 
           $matsub[7]  = 'mill'; 
           $matsub[9]  = 'trill'; 
           $matsub[11] = 'mill'; 
           $matsub[13] = 'bill'; 
           $matsub[15] = 'mill'; 
           $matmil[4]  = 'millones'; 
           $matmil[6]  = 'billones'; 
           $matmil[7]  = 'de billones'; 
           $matmil[8]  = 'millones de billones'; 
           $matmil[10] = 'trillones'; 
           $matmil[11] = 'de trillones'; 
           $matmil[12] = 'millones de trillones'; 
           $matmil[13] = 'de trillones'; 
           $matmil[14] = 'billones de trillones'; 
           $matmil[15] = 'de billones de trillones'; 
           $matmil[16] = 'millones de billones de trillones';   
           $float=explode('.',$num);
           $num=$float[0];
           $num = trim((string)@$num); 
           if ($num[0] == '-') { 
              $neg = 'menos '; 
              $num = substr($num, 1); 
           }else 
              $neg = ''; 
           while ($num[0] == '0') $num = substr($num, 1); 
           if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
           $zeros = true; 
           $punt = false; 
           $ent = ''; 
           $fra = ''; 
           for ($c = 0; $c < strlen($num); $c++) { 
              $n = $num[$c]; 
              if (! (strpos(".,'''", $n) === false)) { 
                 if ($punt) break; 
                 else{ 
                    $punt = true; 
                    continue; 
                 } 
              }elseif (! (strpos('0123456789', $n) === false)) { 
                 if ($punt) { 
                    if ($n != '0') $zeros = false; 
                    $fra .= $n; 
                 }else 
                    $ent .= $n; 
              }else 
                 break;
           } 
           $ent = '     ' . $ent; 
           if ($dec and $fra and ! $zeros) { 
              $fin = ' coma'; 
              for ($n = 0; $n < strlen($fra); $n++) { 
                 if (($s = $fra[$n]) == '0') 
                    $fin .= ' cero'; 
                 elseif ($s == '1') 
                    $fin .= $fem ? ' una' : ' un'; 
                 else 
                    $fin .= ' ' . $matuni[$s]; 
              } 
           }else 
              $fin = ''; 
           if ((int)$ent === 0) return 'Cero ' . $fin; 
           $tex = ''; 
           $sub = 0; 
           $mils = 0; 
           $neutro = true;  
           while ( ($num = substr($ent, -3)) != '   ') { 
              $ent = substr($ent, 0, -3); 
              if (++$sub < 3 and $fem) { 
                 $matuni[1] = 'una'; 
                 $subcent = 'as'; 
              }else{ 
                 $matuni[1] = $neutro ? 'un' : 'uno'; 
                 $subcent = 'os'; 
              } 
              $t = ''; 
              $n2 = substr($num, 1); 
              if ($n2 == '00') { 
              }elseif ($n2 < 21) 
                 $t = ' ' . $matuni[(int)$n2]; 
              elseif ($n2 < 30) { 
                 $n3 = $num[2]; 
                 if ($n3 != 0) $t = 'i' . $matuni[$n3]; 
                 $n2 = $num[1]; 
                 $t = ' ' . $matdec[$n2] . $t; 
              }else{ 
                 $n3 = $num[2]; 
                 if ($n3 != 0) $t = ' y ' . $matuni[$n3]; 
                 $n2 = $num[1]; 
                 $t = ' ' . $matdec[$n2] . $t; 
              } 
              $n = $num[0]; 
              if ($n == 1) { 
                 $t = ' ciento' . $t; 
              }elseif ($n == 5){ 
                 $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t; 
              }elseif ($n != 0){ 
                 $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t; 
              } 
              if ($sub == 1) { 
              }elseif (! isset($matsub[$sub])) { 
                 if ($num == 1) { 
                    $t = ' mil'; 
                 }elseif ($num > 1){ 
                    $t .= ' mil'; 
                 } 
              }elseif ($num == 1) { 
                 $t .= ' ' . $matsub[$sub] . '?n'; 
              }elseif ($num > 1){ 
                 $t .= ' ' . $matsub[$sub] . 'ones'; 
              }   
              if ($num == '000') $mils ++; 
              elseif ($mils != 0) { 
                 if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
                 $mils = 0; 
              }       
              $tex = $t . $tex; 
           } 
           $tex = $neg . substr($tex, 1) . $fin; 
           $end_num=ucfirst($tex).' pesos '.$float[1].'/100 M.N.';
           return $end_num; 
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
