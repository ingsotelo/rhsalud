@extends('layouts.app')
@section('styles')

<style type="text/css">
    table {
        width: 100%;
        display:block;
    }
    thead {
        display: inline-block;
        width: 100%;
        height: 20px;
    }
    tbody {
        height: 200px;
        display: inline-block;
        width: 100%;
        overflow: auto;
    }
    .progress {

      width: 100%;
    }
</style>
@endsection


@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Cargar archivos XML') }}
                            </span>
                            <div class="btn-group pull-right btn-group-xs">
                                <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm float-right">
                                    <i class="fas fa-fw fa-reply" aria-hidden="true"></i>
                                    <span class="hidden-xs">Volver a Panel de Control</span>
                                </a>
                            </div>
                        </div>
                </div>

                <div class="card-body">

                    <form id = 'file-catcher'>
                        <h3><code>1-Seleccione la información de los datos a cargar.</code></h3>
                        <div class="form-group has-feedback row">

                            <div class="col-md-3">
                                <label>Nombre de la nómina:</label>
                                <div class="input-group"> 
                                    <select class="custom-select form-control form-control-sm" name="nombre_nomina" id="nombre_nomina" >
                                        @if ($nominas)
                                            @foreach($nominas as $nombre_nomina)
                                                <option value="{{ $nombre_nomina->clave }}">{{ $nombre_nomina->descripcion }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label>Tipo de la nómina:</label>
                                <div class="input-group">
                                    <select class="custom-select form-control form-control-sm" name="tipo_nomina" id="tipo_nomina" >
                                        @if ($tipos)
                                            @foreach($tipos as $tipo_nomina)
                                                <option value="{{ $tipo_nomina->clave }}">{{ $tipo_nomina->descripcion }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label>Quincena:</label>
                                <div class="input-group">
                                    <select class="custom-select form-control form-control-sm" name="qna_nomina" id="qna_nomina" >
                                        @for ($i = 1; $i <= 24 ; $i++)
                                            <option value="{{ $i }}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                    <label>Año:</label>
                                    <div class="input-group">
                                        <select class="custom-select form-control form-control-sm" name="anio_nomina" id="anio_nomina" >
                                            @for ($i = date("Y"); $i >= 2017  ; $i--)
                                                <option value="{{ $i }}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                            </div>
                        </div>

                        <h3><code>2-Seleccione la carpeta a cargar.</code></h3>
                        <div style="background-color: #eee; padding: 1em;">
                            <input type="file" id="picker" name="fileList" webkitdirectory multiple>
                        </div>

                        <table class="table table-sm" id="table1"></table>

                        <h3><code>3.-Cargar los XML en el servidor.</code></h3>
                        <div style="background-color: #eee; padding: 1em;" id="submit">
                            <button type="submit" id="btnsub">Subir Archivos</button>
                        </div>

                        <table class="table table-sm" id="table2"></table>

                        <div class="progress" style="height: 30px;">
                          <div id="dynamic" 
                               class="progress-bar progress-bar-striped" 
                               role="progressbar" 
                               aria-valuenow="0" 
                               aria-valuemin="0" 
                               aria-valuemax="100" 
                               style="width: 0%">
                               <span id="current-progress"></span>
                          </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript">
    
        var table   = document.getElementById("table1");
        var table2   = document.getElementById("table2");
        var fileInput = document.getElementById('picker');
        var nombre_nomina = document.getElementById("nombre_nomina");
        var tipo_nomina = document.getElementById("tipo_nomina");
        var qna_nomina = document.getElementById("qna_nomina");
        var anio_nomina = document.getElementById("anio_nomina");


        var fileList = [];
        var fileList2 = [];
        var current_progress = 0;
        var file_count = 0;

        fileInput.addEventListener('change', function (evnt) {
            fileList = [];
            fileList2 = [];
            current_progress = 0;
            file_count = 0;


            $("#table1 tr").remove(); 
            $("#table2 tr").remove();
            document.getElementById("btnsub").disabled = false;
            


            $("#dynamic")
            .css("width", current_progress + "%")
            .attr("aria-valuenow", current_progress)
            .text(Math.round(current_progress) + "% Completado");

                for (var i = 0; i < fileInput.files.length; i++) {
                    
                    var row = table.insertRow(-1);
                    var cell0 = row.insertCell(0);
                    var cell1 = row.insertCell(1);
                    var cell2 = row.insertCell(2);
                    cell0.innerHTML = i+1;
                    cell1.innerHTML = fileInput.files[i].name;
                    cell2.innerHTML = fileInput.files[i].type;
                    

                    if (fileInput.files[i].type == "text/xml"){

                        if(fileList.length < 5000)
                            fileList.push(fileInput.files[i]);
                        else 
                            fileList2.push(fileInput.files[i]);

                        row.className = "bg-success";

                    }else{
                        row.className = "bg-danger";
                    }
                }

        });

        var fileCatcher = document.getElementById('file-catcher');

        fileCatcher.addEventListener('submit', function(evnt) {

            document.getElementById("btnsub").disabled = true;
            nombre_nomina.disabled = true;
            tipo_nomina.disabled = true;
            qna_nomina.disabled = true;
            anio_nomina.disabled = true;
            fileInput.disabled = true;

            evnt.preventDefault(); 

            
            if(file_count < 5000){
                fileList.forEach(function (file) {    
                    var formData = new FormData();
                    var request = new XMLHttpRequest();

                    formData.set("_token", '{{ csrf_token() }}');
                    formData.set("file", file);
                    formData.set("path", file.webkitRelativePath);
                    formData.set("nombre_nomina", nombre_nomina.options[nombre_nomina.selectedIndex].value);
                    formData.set("tipo_nomina", tipo_nomina.options[nombre_nomina.selectedIndex].value);
                    formData.set("qna_nomina", qna_nomina.options[nombre_nomina.selectedIndex].value);
                    formData.set("anio_nomina", anio_nomina.options[nombre_nomina.selectedIndex].value);
                    request.open("POST",'{{ route('uploadcfdis') }}');

                    request.onload = function(Event) {
                        if (request.status == 200) {                            
                            var row = table2.insertRow(-1);
                            var cell0 = row.insertCell(0);
                            var cell1 = row.insertCell(1);
                            var cell2 = row.insertCell(2);
                            var cell2 = row.insertCell(3);
                            cell0.innerHTML = table2.getElementsByTagName("tbody")[0].getElementsByTagName("tr").length;;
                            cell1.innerHTML = file.name;
                            cell2.innerHTML = file.type;
                            cell2.innerHTML = '<b>Cargado  </b><i class="fas fa-check" style = "color:green;"></i>';
                            file_count++;
                            current_progress = (file_count*100)/(fileList.length+fileList2.length);
                            $("#dynamic")
                            .css("width", current_progress + "%")
                            .attr("aria-valuenow", current_progress)
                            .text(Math.round(current_progress) + "% Completado");
                            if(file_count == 5000){
                                document.getElementById("btnsub").disabled = false;
                                alert("Por favor vuelva a dar click en el boton Subir Archivos para terminar");
                            } 

                            console.log(file);
                            
                        } else {
                            console.log("Error " + request.status + " occurred when trying to upload your file.");
                        }
                    };

                    request.send(formData);
                    
                });
            }else{
                fileList2.forEach(function (file) {

                    var formData = new FormData();
                    var request = new XMLHttpRequest();

                    formData.set("_token", '{{ csrf_token() }}');
                    formData.set("file", file);
                    formData.set("path", file.webkitRelativePath);
                    formData.set("nombre_nomina", nombre_nomina.options[nombre_nomina.selectedIndex].value);
                    formData.set("tipo_nomina", tipo_nomina.options[nombre_nomina.selectedIndex].value);
                    formData.set("qna_nomina", qna_nomina.options[nombre_nomina.selectedIndex].value);
                    formData.set("anio_nomina", anio_nomina.options[nombre_nomina.selectedIndex].value);
                    request.open("POST",'{{ route('uploadcfdis') }}');

                    request.onload = function(Event) {
                        if (request.status == 200) {    
                            var row = table2.insertRow(-1);
                            var cell0 = row.insertCell(0);
                            var cell1 = row.insertCell(1);
                            var cell2 = row.insertCell(2);
                            var cell2 = row.insertCell(3);
                            cell0.innerHTML = table2.getElementsByTagName("tbody")[0].getElementsByTagName("tr").length;;
                            cell1.innerHTML = file.name;
                            cell2.innerHTML = file.type;
                            cell2.innerHTML = '<b>Cargado  </b><i class="fas fa-check" style = "color:green;"></i>';
                            file_count++;
                            current_progress = (file_count*100)/(fileList.length+fileList2.length);
                            $("#dynamic")
                            .css("width", current_progress + "%")
                            .attr("aria-valuenow", current_progress)
                            .text(Math.round(current_progress) + "% Completado");
                        } else {
                            console.log("Error " + request.status + " occurred when trying to upload your file.");
                        }
                    };
                    request.send(formData);
                });
            }
            
        });

    </script>
@endsection