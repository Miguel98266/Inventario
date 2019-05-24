<template>
            <main class="main">
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Insumos
                        <button type="button" @click="abrirModal('insumo','registrar')" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterio">
                                      <option value="nombre">Nombre</option>
                                      <option value="ubicacion">Ubicacion</option>
                                    </select>
                                    <input type="text" v-model="buscar" @keyup.enter="listarInsumo(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="listarInsumo(1,buscar,criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Codigo</th>
                                    <th>Nombre</th>
                                    <th>Categoria</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Ubicacion</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="insumo in arrayInsumo" :key="insumo.id">
                                    <td>
                                        <button type="button" @click="abrirModal('insumo','actualizar',insumo)" class="btn btn-warning btn-sm">
                                          <i class="icon-pencil"></i>
                                        </button> &nbsp;
                                        <template v-if="insumo.condicion">
                                            <button type="button" class="btn btn-danger btn-sm" @click="desactivarInsumo(insumo.id)">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button type="button" class="btn btn-info btn-sm" @click="activarInsumo(insumo.id)">
                                                <i class="icon-check"></i>
                                            </button>
                                        </template>
                                    </td>
                                    <td v-text="insumo.codigo"></td>
                                    <td v-text="insumo.nombre"></td>
                                    <td v-text="insumo.nombre_categoria"></td>
                                    <td v-text="insumo.precio_insumo"></td>
                                    <td v-text="insumo.stock"></td>
                                    <td v-text="insumo.ubicacion"></td>
                                    
                                    <td>
                                        <div v-if="insumo.condicion">
                                            <span class="badge badge-success">Activo</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-danger">Desactivado</span>
                                        </div>
                                        
                                    </td>
                                </tr>                                
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar,criterio)">Anterior</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar,criterio)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar,criterio)">Siguiente</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Categoria</label>
                                    <div class="col-md-9">
                                        <select class="form-control" v-model="idcategoria">
                                            <option value="0" disabled>Seleccione</option>
                                            <option v-for="categoria in arrayCategoria" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre"></option>
                                        </select>
                                </div>
                                    </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Código</label>
                                    <div class="col-md-9">
                                        <input type="text" v-model="codigo" class="form-control" placeholder="Codigo de barras">
                                        <barcode :value="codigo" :options="{format: 'EAN-13'}">
                                            Generando codigo.
                                        </barcode>
                                    </div>
                                </div>                                
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                                    <div class="col-md-9">
                                        <input type="text" v-model="nombre" class="form-control" placeholder="Nombre de insumo">
                                
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Precio</label>
                                    <div class="col-md-9">
                                        <input type="number" v-model="precio_insumo" class="form-control" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Stock</label>
                                    <div class="col-md-9">
                                        <input type="number" v-model="stock" class="form-control" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="email-input">Ubicación</label>
                                    <div class="col-md-9">
                                        <input type="email" v-model="ubicacion" class="form-control" placeholder="Ingrese la ubicación">
                                    </div>
                                </div>
                                <div v-show="errorInsumo" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjInsumo" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarInsumo()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarInsumo()">Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->
        </main>
</template>

<script>
import VueBarcode from 'vue-barcode';
    export default {
        data (){
            return {
                insumo_id: 0,
                idcategoria: 0,
                nombre_categoria:'',
                codigo:'',
                nombre : '',
                precio_insumo:0,
                stock:0,
                ubicacion : '',
                arrayInsumo : [],
                modal : 0,
                tituloModal : '',
                tipoAccion : 0,
                errorInsumo : 0,
                errorMostrarMsjInsumo : [],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterio : 'nombre',
                buscar : '',
                arrayCategoria:[]
            }
        },
        components: {
    'barcode': VueBarcode
  }, 
        computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumber: function() {
                if(!this.pagination.to) {
                    return [];
                }
                
                var from = this.pagination.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }  

                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;             

            }
        },
        methods : {
            listarInsumo (page,buscar,criterio){
                let me=this;
                var url= '/insumo?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayInsumo = respuesta.insumos.data;
                    me.pagination= respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectCategoria(){
                 let me=this;
                var url= '/categoria/selectCategoria';
                axios.get(url).then(function (response) {
                   // console.log(response);
                    var respuesta= response.data;
                    me.arrayCategoria = respuesta.categorias;
                    
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPagina(page,buscar,criterio){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarInsumo(page,buscar,criterio);
            },
            registrarInsumo(){
                if (this.validarInsumo()){
                    return;
                }
                
                let me = this;

                axios.post('/insumo/registrar',{
                    'idcategoria':this.idcategoria,
                    'codigo':this.codigo,
                    'nombre': this.nombre,
                    'stock':this.stock,
                    'precio_insumo':this.precio_insumo,
                    'ubicacion': this.ubicacion
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarInsumo(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                });
            },
            actualizarInsumo(){
               if (this.validarInsumo()){
                    return;
                }
                
                let me = this;

                axios.put('/insumo/actualizar',{
                    'idcategoria':this.idcategoria,
                    'codigo':this.codigo,
                    'nombre': this.nombre,
                    'stock':this.stock,
                    'precio_insumo':this.precio_insumo,
                    'ubicacion': this.ubicacion,
                    'id':this.insumo_id
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarInsumo(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                }); 
            },
              desactivarInsumo(id){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false,
                })
                swalWithBootstrapButtons.fire({
                title: 'Estas seguro de desactivar este insumo?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar!',
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                     let me = this;
                    axios.put('/insumo/desactivar',{
                        'id': id
                    }).then(function (response) {
                      me.listarInsumo(1,'','nombre');
                         swalWithBootstrapButtons.fire(
                            'Desactivado!',
                            'El registro a sido desactivado con éxito.',
                            'success'
                            )
                    }).catch(function (error) {
                        console.log(error);
                    });

                   
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                   
                }
                })
            },
            activarInsumo(id){
                const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false,
                })

                swalWithBootstrapButtons.fire({
                title: 'Estas seguro de Activar este insumo?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar!',
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                     let me = this;

                    axios.put('/insumo/activar',{
                        'id': id
                    }).then(function (response) {
                         me.listarInsumo(1,'','nombre');
                         swalWithBootstrapButtons.fire(
                            'Activado!',
                            'El registro a sido activado con éxito.',
                            'success'
                            )
                    }).catch(function (error) {
                        console.log(error);
                    });

                   
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                   
                }
                })
            },

            validarInsumo(){
                this.errorInsumo=0;
                this.errorMostrarMsjInsumo =[];

                if(this.idcategoria==0)this.errorMostrarMsjInsumo.push('Seleccione una categoria');
                if (!this.nombre) this.errorMostrarMsjInsumo.push("El nombre del insumo no puede estar vacío.");
                if (!this.stock) this.errorMostrarMsjInsumo.push("El stock del insumo debe ser un número y no puede estar vacio.");
                if (!this.precio_insumo) this.errorMostrarMsjInsumo.push("El precio del insumo debe ser un número y no puede estar vacio.");

                if (this.errorMostrarMsjInsumo.length) this.errorInsumo = 1;

                return this.errorInsumo;
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.idcategoria=0;
                this.nombre_categoria='';
                this.codigo='';
                this.nombre='';
                this.precio_insumo=0;
                this.stock=0;
                this.ubicacion='';
                this.errorInsumo=0;
            },
            abrirModal(modelo, accion, data = []){
                switch(modelo){
                    case "insumo":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar Insumo';
                                this.idcategoria=0;
                                this.nombre_categoria='';
                                this.codigo='';
                                this.nombre= '';
                                this.precio_insumo=0;
                                this.stock=0;
                                this.ubicacion='';
                                
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar':
                            {
                                //console.log(data);
                                this.modal=1;
                                this.tituloModal='Actualizar Insumo ';
                                this.tipoAccion=2;
                                this.insumo_id=data['id'];
                                this.idcategoria=data['idcategoria'];
                                this.codigo=data['codigo'];
                                this.nombre =data['nombre'];
                                this.stock=data['stock'];
                                this.precio_insumo=data['precio_insumo'];
                                this.ubicacion= data['ubicacion'];
                                break;
                            }
                        }
                    }
                }
                this.selectCategoria();
            }
        },
        mounted() {
            this.listarInsumo(1,this.buscar,this.criterio);
        }
    }
</script>
<style>    
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .div-error{
        display: flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
</style>
