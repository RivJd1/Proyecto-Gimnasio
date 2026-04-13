<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({ clients: Array });

const search = ref('');
const selectedClient = ref(null);
const paymentDialog = ref(false);

const filtered = computed(() =>
    props.clients.filter(c => {
        const q = search.value.toLowerCase();
        return (
            `${c.nombre} ${c.apellido}`.toLowerCase().includes(q) ||
            c.cedula?.includes(q) ||
            c.telefono?.includes(q)
        );
    })
);

const form = useForm({
    membership_id: null,
    monto:         0,
    metodo_pago:   '',
    payment_id:    null,
});

const openPayment = (client) => {
    selectedClient.value = client;
    form.membership_id = client.membresia_id;
    form.monto         = client.en_mora ? client.monto_pendiente : client.precio_plan;
    form.payment_id    = client.en_mora ? client.pago_vencido_id : null;
    form.metodo_pago   = '';
    paymentDialog.value = true;
};

const submit = () => {
    form.post(route('payments.store'), {
        onSuccess: () => { paymentDialog.value = false; },
    });
};

const estadoColor = (client) => {
    if (!client.membresia_id) return 'secondary';
    if (client.en_mora) return 'error';
    return 'success';
};

const estadoLabel = (client) => {
    if (!client.membresia_id) return 'Sin membresía';
    if (client.en_mora) return `En mora · ${client.dias_atraso}d`;
    return 'Al día';
};
</script>

<template>
    <Head title="Cobranza" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">Cobranza</h1>
            <p class="text-gray-400 text-sm mt-1">Gestión de pagos de membresías</p>
        </div>

        <!-- Buscador -->
        <v-card color="#1f2937" rounded="lg" elevation="0" class="mb-4 pa-4">
            <v-text-field
                v-model="search"
                placeholder="Buscar cliente por nombre, cédula o teléfono..."
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                hide-details
                color="primary"
                clearable
            />
        </v-card>

        <!-- Tabla de clientes -->
        <v-card color="#1f2937" rounded="lg" elevation="0">
            <v-data-table
                :headers="[
                    { title: 'Cliente',      key: 'nombre' },
                    { title: 'Plan',         key: 'plan' },
                    { title: 'Vence',        key: 'fecha_fin' },
                    { title: 'Estado',       key: 'estado' },
                    { title: 'Acción',       key: 'actions', sortable: false },
                ]"
                :items="filtered"
                theme="dark"
                density="comfortable"
                hover
            >
                <template #item.nombre="{ item }">
                    <div class="font-medium text-white">{{ item.nombre }} {{ item.apellido }}</div>
                    <div class="text-gray-400 text-xs">{{ item.cedula }}</div>
                </template>

                <template #item.plan="{ item }">
                    <span class="text-gray-300 text-sm">{{ item.plan ?? '—' }}</span>
                </template>

                <template #item.fecha_fin="{ item }">
                    <span class="text-gray-300 text-sm">{{ item.fecha_fin ?? '—' }}</span>
                </template>

                <template #item.estado="{ item }">
                    <v-chip
                        :color="estadoColor(item)"
                        size="small"
                        variant="tonal"
                    >
                        {{ estadoLabel(item) }}
                    </v-chip>
                </template>

                <template #item.actions="{ item }">
                    <v-btn
                        v-if="item.membresia_id"
                        :color="item.en_mora ? 'error' : 'primary'"
                        size="small"
                        variant="tonal"
                        prepend-icon="mdi-cash-register"
                        @click="openPayment(item)"
                    >
                        {{ item.en_mora ? 'Cobrar mora' : 'Registrar pago' }}
                    </v-btn>
                    <span v-else class="text-gray-500 text-sm">Sin membresía</span>
                </template>

                <template #no-data>
                    <div class="text-center py-10 text-gray-500">
                        <v-icon size="48" class="mb-2">mdi-account-search-outline</v-icon>
                        <p>No se encontraron clientes.</p>
                    </div>
                </template>
            </v-data-table>
        </v-card>

        <!-- Dialog de cobro -->
        <v-dialog v-model="paymentDialog" max-width="460">
            <v-card color="#1f2937" rounded="lg">
                <v-card-title class="pa-5 pb-2 text-white flex items-center gap-2">
                    <v-icon :color="selectedClient?.en_mora ? 'error' : 'primary'">
                        mdi-cash-register
                    </v-icon>
                    {{ selectedClient?.en_mora ? 'Cobro de mora' : 'Registrar pago' }}
                </v-card-title>

                <v-card-text class="pa-5 pt-2">
                    <!-- Alerta de mora -->
                    <v-alert
                        v-if="selectedClient?.en_mora"
                        type="error"
                        variant="tonal"
                        density="compact"
                        class="mb-4"
                    >
                        Cliente con <strong>{{ selectedClient.dias_atraso }} días</strong> de atraso.
                    </v-alert>

                    <!-- Ficha resumen -->
                    <div class="pa-3 rounded-lg mb-4" style="background: #111827">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-400 text-sm">Cliente</span>
                            <span class="text-white text-sm font-medium">
                                {{ selectedClient?.nombre }} {{ selectedClient?.apellido }}
                            </span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-400 text-sm">Plan</span>
                            <span class="text-white text-sm">{{ selectedClient?.plan }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">Monto a cobrar</span>
                            <span class="text-green-400 text-sm font-bold">
                                L. {{ Number(form.monto ?? 0).toFixed(2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Método de pago -->
                    <p class="text-gray-400 text-sm mb-3">Método de pago:</p>
                    <v-row>
                        <v-col cols="6">
                            <v-card
                                :color="form.metodo_pago === 'efectivo' ? 'primary' : '#111827'"
                                rounded="lg"
                                elevation="0"
                                class="cursor-pointer border-2"
                                :class="form.metodo_pago === 'efectivo' ? 'border-green-400' : 'border-gray-700'"
                                @click="form.metodo_pago = 'efectivo'"
                            >
                                <v-card-text class="pa-4 text-center">
                                    <v-icon :color="form.metodo_pago === 'efectivo' ? 'white' : 'success'">
                                        mdi-cash
                                    </v-icon>
                                    <div class="text-white text-sm mt-1">Efectivo</div>
                                </v-card-text>
                            </v-card>
                        </v-col>
                        <v-col cols="6">
                            <v-card
                                :color="form.metodo_pago === 'tarjeta' ? 'primary' : '#111827'"
                                rounded="lg"
                                elevation="0"
                                class="cursor-pointer border-2"
                                :class="form.metodo_pago === 'tarjeta' ? 'border-green-400' : 'border-gray-700'"
                                @click="form.metodo_pago = 'tarjeta'"
                            >
                                <v-card-text class="pa-4 text-center">
                                    <v-icon :color="form.metodo_pago === 'tarjeta' ? 'white' : 'info'">
                                        mdi-credit-card
                                    </v-icon>
                                    <div class="text-white text-sm mt-1">Tarjeta</div>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                    <p v-if="form.errors.metodo_pago" class="text-red-400 text-sm mt-2">
                        {{ form.errors.metodo_pago }}
                    </p>
                </v-card-text>

                <v-card-actions class="pa-5 pt-0 gap-2">
                    <v-spacer />
                    <v-btn variant="tonal" color="secondary" @click="paymentDialog = false">
                        Cancelar
                    </v-btn>
                    <v-btn
                        color="success"
                        prepend-icon="mdi-check"
                        :disabled="!form.metodo_pago"
                        :loading="form.processing"
                        @click="submit"
                    >
                        Confirmar pago
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AuthenticatedLayout>
</template>
