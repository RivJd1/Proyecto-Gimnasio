<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    desde:             String,
    hasta:             String,
    totalIngresos:     Number,
    desglose:          Object,
    montoPendiente:    Number,
    clientesEnMora:    Array,
    clientesNuevos:    Number,
    ingresosMensuales: Array,
});

const desde = ref(props.desde);
const hasta = ref(props.hasta);

const filtrar = () => {
    router.get(route('finances.index'), { desde: desde.value, hasta: hasta.value }, { preserveState: true });
};

const tipoLabel = { mensual: 'Mensual', semestral: '6 Meses', anual: 'Anual', otro: 'Otro' };

const printReport = () => window.print();

// Simple bar chart via inline styles
const maxMensual = Math.max(...props.ingresosMensuales.map(m => m.total), 1);
</script>

<template>
    <Head title="Finanzas" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Consultas Financieras</h1>
                <p class="text-gray-400 text-sm mt-1">Resumen de ingresos y estado de cuentas</p>
            </div>
            <v-btn color="secondary" variant="tonal" prepend-icon="mdi-printer" @click="printReport">
                Imprimir
            </v-btn>
        </div>

        <!-- Filtro de fechas -->
        <v-card color="#1f2937" rounded="lg" elevation="0" class="mb-6 pa-4">
            <v-row align="center">
                <v-col cols="12" sm="4">
                    <v-text-field v-model="desde" label="Desde" type="date" variant="outlined" density="compact" hide-details color="primary" />
                </v-col>
                <v-col cols="12" sm="4">
                    <v-text-field v-model="hasta" label="Hasta" type="date" variant="outlined" density="compact" hide-details color="primary" />
                </v-col>
                <v-col cols="12" sm="4">
                    <v-btn color="primary" block @click="filtrar" prepend-icon="mdi-filter">
                        Filtrar
                    </v-btn>
                </v-col>
            </v-row>
        </v-card>

        <!-- KPI Cards -->
        <v-row class="mb-6">
            <v-col cols="12" sm="6" lg="3">
                <v-card color="#1f2937" rounded="lg" elevation="0">
                    <v-card-text class="pa-5">
                        <div class="text-gray-400 text-xs mb-1">Ingresos del período</div>
                        <div class="text-white text-3xl font-bold">L. {{ totalIngresos.toFixed(2) }}</div>
                        <v-icon color="success" size="20" class="mt-1">mdi-trending-up</v-icon>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" lg="3">
                <v-card color="#1f2937" rounded="lg" elevation="0">
                    <v-card-text class="pa-5">
                        <div class="text-gray-400 text-xs mb-1">Monto pendiente (mora)</div>
                        <div class="text-red-400 text-3xl font-bold">L. {{ montoPendiente.toFixed(2) }}</div>
                        <v-icon color="error" size="20" class="mt-1">mdi-alert-circle</v-icon>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" lg="3">
                <v-card color="#1f2937" rounded="lg" elevation="0">
                    <v-card-text class="pa-5">
                        <div class="text-gray-400 text-xs mb-1">Clientes en mora</div>
                        <div class="text-yellow-400 text-3xl font-bold">{{ clientesEnMora.length }}</div>
                        <v-icon color="warning" size="20" class="mt-1">mdi-account-alert</v-icon>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" lg="3">
                <v-card color="#1f2937" rounded="lg" elevation="0">
                    <v-card-text class="pa-5">
                        <div class="text-gray-400 text-xs mb-1">Clientes nuevos</div>
                        <div class="text-blue-400 text-3xl font-bold">{{ clientesNuevos }}</div>
                        <v-icon color="info" size="20" class="mt-1">mdi-account-plus</v-icon>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-row>
            <!-- Desglose por tipo de contrato -->
            <v-col cols="12" md="5">
                <v-card color="#1f2937" rounded="lg" elevation="0" class="pa-5 h-full">
                    <div class="text-white font-semibold mb-4">Ingresos por tipo de contrato</div>
                    <div
                        v-for="(data, tipo) in desglose"
                        :key="tipo"
                        class="mb-4"
                    >
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-300">{{ tipoLabel[tipo] ?? tipo }}</span>
                            <span class="text-white font-medium">L. {{ Number(data.total).toFixed(2) }}</span>
                        </div>
                        <v-progress-linear
                            :model-value="totalIngresos > 0 ? (data.total / totalIngresos) * 100 : 0"
                            color="primary"
                            bg-color="#374151"
                            rounded
                            height="8"
                        />
                        <div class="text-gray-500 text-xs mt-1">{{ data.cantidad }} pago(s)</div>
                    </div>
                    <div v-if="Object.keys(desglose).length === 0" class="text-gray-500 text-sm text-center py-4">
                        Sin ingresos en este período.
                    </div>
                </v-card>
            </v-col>

            <!-- Gráfica de barras mensual -->
            <v-col cols="12" md="7">
                <v-card color="#1f2937" rounded="lg" elevation="0" class="pa-5 h-full">
                    <div class="text-white font-semibold mb-4">Ingresos últimos 6 meses</div>
                    <div class="flex items-end gap-3 h-40">
                        <div
                            v-for="mes in ingresosMensuales"
                            :key="mes.mes"
                            class="flex-1 flex flex-col items-center gap-1"
                        >
                            <span class="text-gray-400 text-xs">L.{{ (mes.total / 1000).toFixed(1) }}k</span>
                            <div
                                class="w-full rounded-t-md bg-green-500 transition-all"
                                :style="{ height: maxMensual > 0 ? `${(mes.total / maxMensual) * 120}px` : '4px' }"
                            />
                            <span class="text-gray-400 text-xs">{{ mes.mes }}</span>
                        </div>
                    </div>
                </v-card>
            </v-col>
        </v-row>

        <!-- Tabla clientes en mora -->
        <v-card color="#1f2937" rounded="lg" elevation="0" class="mt-6">
            <v-card-title class="pa-5 pb-0 flex items-center gap-2">
                <v-icon color="error">mdi-account-alert</v-icon>
                <span class="text-white text-base font-semibold">Clientes en mora</span>
                <v-chip color="error" size="small" class="ml-2">{{ clientesEnMora.length }}</v-chip>
            </v-card-title>
            <v-card-text class="pa-5 pt-3">
                <v-data-table
                    v-if="clientesEnMora.length > 0"
                    :headers="[
                        { title: 'Cliente', key: 'nombre' },
                        { title: 'Cédula',  key: 'cedula' },
                    ]"
                    :items="clientesEnMora"
                    theme="dark"
                    density="comfortable"
                    hide-default-footer
                />
                <div v-else class="text-center py-6 text-gray-500">
                    <v-icon size="36" class="mb-1">mdi-check-circle-outline</v-icon>
                    <p>No hay clientes en mora.</p>
                </div>
            </v-card-text>
        </v-card>
    </AuthenticatedLayout>
</template>
