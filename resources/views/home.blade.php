@extends('layouts.app')

@section('styles')    

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

@endsection


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Recibos de Nómina de {{ Auth::user()->full_name }}</div>

                <div class="card-body">

                  <table width="100%" class="display" id = "data-table" cellspacing="0">
                            
                  </table>

                <p>
                  <br><br>
                  <b>INFORMACIÓN IMPORTANTE:</b>
                  <br>
                  <ul>
                    <li>
                      ¿Quieres saber como consultar y recuperar tus recibos de nómina a través del portal del SAT? <a href="https://www.sat.gob.mx/consulta/25242/consulta-tus-recibos-de-nomina-a-traves-del-portal-del-sat">esta información te interesa.</a> 
                    </li>
                    <li>
                      En el mes de abril se presenta la Declaración Anual de personas físicas con ingresos por salarios. <a href="https://www.sat.gob.mx/declaracion/24764/declaracion-anual-de-personas-fisicas-con-ingresos-por-salarios">¿Quieres saber mas?</a>
                    </li>
                    <li>
                      ¿Quieres saber como tus ingresos y retenciones? <a href="https://www.youtube.com/watch?time_continue=2&v=Y-hsbe4zl28"> utiliza el Visor de Nómina 2018 del SAT.</a> 
                    </li>
                  </ul>
                </p>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
  <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {

      var dataSet = {!!json_encode($cfdis)!!};

      var table = $('#data-table').DataTable({
                      data: dataSet,
                      columns: [
                          { data: "year", title: "Año"},
                          { data: "qna", title: "Quincena"},
                          { data: "nomina", title: "Nómina"},
                          { data: "tipo", targets: 0, 
                            render:   function ( data, type, row, meta ) {
                              if (data === "11")
                                return "Ordinaria";
                              else if (data === "55")
                                return "Extraordinaria Aguinaldo";
                              else if (data === "66")
                                return "Extraordinaria";
                              else if (data === "22")
                                return "Retroactivo";
                              else if (data === "6R")
                                return "Extraordinaria Dia de Reyes";
                              else
                                return "No identificada";
                            }
                          },
                          { data: null, targets: 0,title: "CFDI",
                            render:   function ( data, type, row, meta) {
                              url = "{{ URL::to('downloadxml/:id') }}";
                              url = url.replace(':id', data.id);
                              return '<a href="'+url+'">XML <i class="fas fa-file-code" style="color:green;"></i></a>';
                              }
                          },
                          { data: null, targets: 0,title: "Recibo",
                            render:   function ( data, type, row, meta) {
                              url = "{{ URL::to('downloadpdf/:id') }}";
                              url = url.replace(':id', data.id);
                              return '<a href="'+url+'">PDF <i class="fas fa-file-pdf" style="color:red;"></i></a>';
                              }
                          },
                      ],
                      language: {
                          url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                      }
              });
    });
  </script>
@endsection


