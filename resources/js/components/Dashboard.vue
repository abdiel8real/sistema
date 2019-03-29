<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escrituorio</a></li>
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-chart">
                                <div class="card-header">
                                    <h4>Ingresos</h4>
                                </div>
                                <div class="card-content">
                                    <div class="ct-chart">
                                        <canvas id="ingresos">

                                        </canvas>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Compras de los últimos meses.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-chart">
                                <div class="card-header">
                                    <h4>Ventas</h4>
                                </div>
                                <div class="card-content">
                                    <div class="ct-chart">
                                        <canvas id="ventas">

                                        </canvas>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Ventas de los últimos meses.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
    export default {
        data (){
            return {
                varIngreso: null,
                charIngreso: null,
                ingresos: [],
                varTotalIngreso: [],
                varMesIngreso: []
            }
        },
        methods: {
            getIngresos(){
                let me = this;
                const url = '/dashboard';
                axios.get(url).then(function (response) {
                    const respuesta = response.data;
                    me.ingresos = respuesta.ingresos;

                    me.loadIngresos();
                })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            loadIngresos(){
                let me = this;
                me.ingresos.map(function (ingreso) {
                    me.varMesIngreso.push(ingreso.mes);
                    me.varTotalIngreso.push(ingreso.total);
                });
                me.varIngreso = document.getElementById('ingresos').getContext('2d');
                me.charIngreso = new Chart(me.varIngreso, {
                    type: 'bar',
                    data: {
                        labels: me.varMesIngreso,
                        datasets: [{
                            label: 'Ingresos',
                            data: me.varTotalIngreso,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
            }
        },
        mounted() {
            this.getIngresos();
        }
    }
</script>

<style scoped>

</style>