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
                        <i class="fa fa-align-justify"></i> Compras
                        <button type="button" @click="mostrarDetalle()" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                <!-- Listado -->
                    <template v-if="listado==1">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterio">
                                      <option value="tipo_comprobante">Tipo Comprobante</option>
                                      <option value="serie_comprobante">Serie comprobante</option>
                                      <option value="fecha_hora">Fecha</option>
                                    </select>
                                    <input type="text" v-model="buscar" @keyup.enter="listarCompra(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="listarCompra(1,buscar,criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Usuario</th>
                                        <th>Proveedor</th>
                                        <th>Tipo comprobante</th>
                                        <th>Serie comprobante</th>
                                        <th>Fecha Hora</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="compra in arrayCompra" :key="compra.id">
                                        <td>
                                            <button type="button" @click="verCompra(compra.id)" class="btn btn-success btn-sm">
                                            <i class="icon-eye"></i>
                                            </button> &nbsp;
                                            <template v-if="compra.estado=='Registrado'">
                                                <button type="button" class="btn btn-danger btn-sm" @click="desactivarCompra(compra.id)">
                                                    <i class="icon-trash"></i>
                                                </button>
                                            </template>
                                        </td>
                                        <td v-text="compra.usuario"></td>
                                        <td v-text="compra.nombre"></td>
                                        <td v-text="compra.tipo_comprobante"></td>
                                        <td v-text="compra.serie_comprobante"></td>
                                        <td v-text="compra.fecha_hora"></td>
                                        <td v-text="compra.total"></td>
                                        <td v-text="compra.estado"></td>
                                    </tr>                                
                                </tbody>
                            </table>
                        </div>
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
                    </template>
                <!-- Fin Listado -->
                <!-- Detalle -->
                    <template v-else-if="listado==0">
                    <div class="card-body">
                        <div class="form-group row border">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Proveedor(*)</label>
                                   <v-select                                      
                                    @search="selectProveedor"
                                    label="nombre"
                                    :filterable="false"
                                    :options="arrayProveedor"
                                    placeholder="Buscar Proveedores"
                                    @input="getDatosProveedor">                                     
                                    <div slot="no-options">Lo sentimos, no hay proveedores con ese nombre.</div>
                                    </v-select>
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Tipo comprobante(*)</label>
                                    <select class="form-control" v-model="tipo_comprobante">
                                        <option value="">Seleccione</option>
                                        <option value="FACTURA">Factura</option>
                                        <option value="TICKET">Ticket</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Serie Comprobante(*)</label>
                                    <input type="text" class="form-control" v-model="serie_comprobante" placeholder="000x">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div v-show="errorCompra" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjCompra" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-group row border">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Insumo <span style="color:red;" v-show="idinsumo==0">(*Seleccione)</span></label>
                                    <div class="form-inline">
                                        <input type="text" class="form-control" v-model="codigo" @keyup.enter="buscarInsumo()" placeholder="Ingresar Insumo">
                                        <button @click="abrirModal()" class="btn btn-primary">...</button>
                                        <input type="text" readonly class="form-control" v-model="insumo">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Precio<span style="color:red;" v-show="precio==0">(*Ingrese)</span></label>
                                    <input type="number" value="0" step="any" class="form-control" v-model="precio">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Cantidad<span style="color:red;" v-show="cantidad==0">(*Ingrese)</span></label>
                                    <input type="number" value="0" class="form-control" v-model="cantidad">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button @click="agregarDetalle()" class="btn btn-success form-control btnagregar"><i class="icon-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <th>Opciones</th>
                                        <th>Insumo</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>SubTotal</th>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                        <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                            <td>
                                                <button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm"> 
                                                    <i class="icon-close"></i>
                                                    </button>
                                            </td>
                                            <td v-text="detalle.insumo">
                                                
                                            </td>
                                            <td>
                                                <input v-model="detalle.precio" type="number" value="3" class="form-control">
                                            </td>
                                            <td>
                                                 <input v-model="detalle.cantidad" type="number" value="2" class="form-control">
                                            </td>
                                            <td>
                                                {{detalle.precio*detalle.cantidad}}
                                            </td>
                                        </tr>
                                        <tr style="background-color: #CEECF5;">
                                            <td colspan="3" align="right"><strong>Total:</strong></td>
                                            <td> $ {{total=calcularTotal}}</td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="5">
                                                No hay Insumos agregados
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <div class="col-md-12">
                                <button type="button" @click="ocultarDetalle()" class="btn btn-secundary">Cerrar</button>
                                <button type="button" class="btn btn-primary" @click="registrarCompra()">Registrar Compra</button>
                            </div>
                        </div>
                    </div>
                    </template>
                <!--Fin Detalle -->
                <!--Ver compra -->
                <template v-else-if="listado==2">
                    <div class="card-body">
                        <div class="form-group row border">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Proveedor</label>
                                   <p v-text="proveedor"></p>
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Tipo comprobante</label>
                                    <p v-text="tipo_comprobante"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Serie Comprobante</label>
                                   <p v-text="serie_comprobante"></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row ">
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <th>Insumo</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>SubTotal</th>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                        <tr v-for="detalle in arrayDetalle" :key="detalle.id">
                                            
                                            <td v-text="detalle.insumo">
                                                
                                            </td>
                                            <td v-text="detalle.precio">                                              
                                            </td>
                                            <td v-text="detalle.cantidad">  
                                            </td>
                                            <td>
                                                {{detalle.precio*detalle.cantidad}}
                                            </td>
                                        </tr>
                                        <tr style="background-color: #CEECF5;">
                                            <td colspan="3" align="right"><strong>Total:</strong></td>
                                            <td> $ {{total}}</td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="5">
                                                No hay Insumos agregados
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                            <div class="form-group row ">
                                <div class="col-md-12">
                                    <button type="button" @click="ocultarDetalle()" class="btn btn-secundary">Cerrar</button>
                                </div>
                            </div>
                    </div>
                    </template>
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
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select class="form-control col-md-3" v-model="criterioA">
                                        <option value="nombre">Nombre</option>
                                        <option value="ubicacion">Ubicacion</option>
                                        <option value="codigo">Codigo</option>
                                        </select>
                                        <input type="text" v-model="buscarA" @keyup.enter="listarInsumo(buscarA,criterioA)" class="form-control" placeholder="Texto a buscar">
                                        <button type="submit" @click="listarInsumo(buscarA,criterioA)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                    </div>
                                </div>
                             </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Código</th>
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
                                            <button type="button" @click="agregarDetalleModal(insumo)" class="btn btn-success btn-sm">
                                            <i class="icon-check"></i>
                                            </button> 
                                            
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
                            </div>
                        </div>
                       <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarPersona()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarPersona()">Actualizar</button>
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
    import Vue from 'vue'
    import vSelect from 'vue-select';
    import 'vue-select/dist/vue-select.css';
    export default {
        data (){
            return {
                compra_id: 0,
                id_proveedor:0,
                proveedor:'',
                nombre : '',
                tipo_comprobante : 'FACTURA',
                serie_comprobante:'',
                total:0.0,
                arrayCompra:[],
                arrayDetalle:[],
                arrayProveedor:[],
                listado:1,
                modal : 0,
                tituloModal : '',
                tipoAccion : 0,
                errorCompra : 0,
                errorMostrarMsjCompra : [],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterio : 'serie_comprobante',
                buscar : '',
                criterioA:'nombre',
                buscarA:'',
                arrayInsumo:[],
                idinsumo:0,
                codigo:'',
                insumo:'',
                precio:0,
                cantidad:0
            }
        },
        components:{
            vSelect
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

            },
            calcularTotal:function(){
                var resultado=0.0;
                for(var i=0;i<this.arrayDetalle.length;i++){
                    resultado=resultado+(this.arrayDetalle[i].precio*this.arrayDetalle[i].cantidad)
                }
                return resultado;
            }
        },
        methods : {
            listarCompra (page,buscar,criterio){
                let me=this;
                var url= '/compra?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayCompra = respuesta.compras.data;
                    me.pagination= respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectProveedor(search,loading){
                let me=this;
                loading(true)

                var url= '/proveedor/selectProveedor?filtro='+search;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    q: search
                    me.arrayProveedor=respuesta.proveedores;
                    loading(false)
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            getDatosProveedor(vall){
                let me=this;
                me.loading=true;
                me.idproveedor=vall.id 
            },
            buscarInsumo(){
                let me=this;
                var url='/insumo/buscarInsumo?filtro='+me.codigo;

                axios.get(url).then(function(response){
                    var respuesta=response.data;
                    me.arrayInsumo=respuesta.insumos;

                    if(me.arrayInsumo.length>0){
                        me.insumo=me.arrayInsumo[0]['nombre'];
                        me.idinsumo=me.arrayInsumo[0]['id'];
                    }
                    else{
                        me.insumo='No existe Insumo';
                        me.idinsumo=0;
                    }
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
                me.listarCompra(page,buscar,criterio);
            },
            encuentra(id){
                var sw=0;
                 for(var i=0;i<this.arrayDetalle.length;i++){
                     if(this.arrayDetalle[i].idinsumo==id){
                        sw=true;
                    }
                 }
                 return sw;
            },
            eliminarDetalle(index){
                let me =this;
                me.arrayDetalle.splice(index,1);
            },
            agregarDetalle(){
                let me=this;
                if(me.idinsumo==0 || me.cantidad==0 || me.precio==0){
                }
                else{
                    if(me.encuentra(me.idinsumo)){
                         Swal.fire({
                                    type: 'error',
                                    title: 'Error...',
                                    text: 'Ese insumo ya esta agregado!',
                                    })
                    }
                    else{
                            me.arrayDetalle.push({
                            idinsumo: me.idinsumo,
                            insumo: me.insumo,
                            cantidad: me.cantidad,
                            precio: me.precio
                            });
                            me.codigo="";
                            me.idinsumo=0;
                            me.insumo="";
                            me.cantidad=0;
                            me.precio=0; 
                        }
                    
                }
            },
            agregarDetalleModal(data=[]){
              let me=this;
              if(me.encuentra(data['id'])){
                         Swal.fire({
                                    type: 'error',
                                    title: 'Error...',
                                    text: 'Ese insumo ya esta agregado!',
                                    })
                    }
                    else{
                            me.arrayDetalle.push({
                            idinsumo:data['id'],
                            insumo: data['nombre'],
                            cantidad: 1,
                            precio: 1
                            });
                            
                        }  
            },
            listarInsumo (buscar,criterio){
                let me=this;
                var url= '/insumo/listarInsumo?buscar='+ buscar + '&criterio='+ criterio;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayInsumo = respuesta.insumos.data;
                    
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            registrarCompra(){
                if (this.validarCompra()){
                    return;
                }
                
                let me = this;

                axios.post('/compra/registrar',{
                   'idproveedor': this.idproveedor,
                    'tipo_comprobante': this.tipo_comprobante,
                    'serie_comprobante' : this.serie_comprobante,
                    'total' : this.total,
                    'data': this.arrayDetalle

                }).then(function (response) {
                    me.listado=1;
                    me.listarCompra(1,'','serie_comprobante');
                    me.idproveedor=0;
                    me.tipo_comprobante='FACTURA';
                    me.serie_comprobante='';
                    me.total=0.0;
                    me.idinsumo=0;
                    me.insumo='';
                    me.cantidad=0;
                    me.precio=0;
                    me.arrayDetalle=[];
                }).catch(function (error) {
                    console.log(error);
                });
            },          
              
            validarCompra(){
                this.errorCompra=0;
                this.errorMostrarMsjCompra =[];

               if (this.idproveedor==0) this.errorMostrarMsjCompra.push("Seleccione un Proveedor");
                if (this.tipo_comprobante==0) this.errorMostrarMsjCompra.push("Seleccione el comprobante");
                if(!this.serie_comprobante)this.errorMostrarMsjCompra.push("Ingrese la serie del comprobante");
                if(this.arrayDetalle.length<=0)this.errorMostrarMsjCompra.push("Ingrese Insumos");


                if (this.errorMostrarMsjCompra.length) this.errorCompra = 1;


                return this.errorCompra;
            },
            mostrarDetalle(){
                let me=this; 
                this.listado=0;

                me.idproveedor=0;
                me.tipo_comprobante='FACTURA';
                me.serie_comprobante='';
                me.total=0.0;
                me.idinsumo=0;
                me.insumo='';
                me.cantidad=0;
                me.precio=0;
                me.arrayDetalle=[];
            },
            ocultarDetalle(){
                this.listado=1;
            },
            verCompra(id){
               let me=this;
               me.listado=2;
                //Obtener datos del ingreso
                var arrayCompraT=[];
                var url= '/compra/obtenerCabecera?id=' + id;
                
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    arrayCompraT = respuesta.compra;

                    me.proveedor=arrayCompraT[0]['nombre'];
                    me.tipo_comprobante=arrayCompraT[0]['tipo_comprobante'];
                    me.serie_comprobante=arrayCompraT[0]['serie_comprobante'];
                    me.total=arrayCompraT[0]['total'];
                   
                })
                .catch(function (error) {
                    console.log(error);
                });
                //Obtener datos de los detalles

                var urld= '/compra/obtenerDetalles?id=' + id;
                
                axios.get(urld).then(function (response) {
                    console.log(response);
                    var respuesta= response.data;
                    me.arrayDetalle = respuesta.detalles;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                
            },
            abrirModal(){
                this.arrayInsumo=[];
                this.modal = 1;
                this.tituloModal = 'Seleccione uno o varios Insumos';
                               
            },
                desactivarCompra(id){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false,
                })
                swalWithBootstrapButtons.fire({
                title: 'Estas seguro de cancelar esta Compra?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar!',
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                     let me = this;
                    axios.put('/compra/desactivar',{
                        'id': id
                    }).then(function (response) {
                      me.listarCompra(1,'','serie_comprobante');
                         swalWithBootstrapButtons.fire(
                            'Cancelado!',
                            'La compra ha sido cancelada con éxito.',
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
        },
        mounted() {
            this.listarCompra(1,this.buscar,this.criterio);
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
    @media (min-width: 600px) {
        .btnagregar{
            margin-top: 2rem;
        }
    }

    
</style>