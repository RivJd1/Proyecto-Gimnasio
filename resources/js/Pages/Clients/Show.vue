<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ client: Object });

const activeMembership = computed(() =>
    props.client.memberships?.find(m => m.estado === 'activa') ?? null
);

const overduePayments = computed(() =>
    props.client.memberships?.flatMap(m => m.payments ?? [])
        .filter(p => p.estado === 'vencido') ?? []
);

const allPayments = computed(() =>
    props.client.memberships?.flatMap(m =>
        (m.payments ?? []).map(p => ({ ...p, plan: m.plan?.nombre }))
    ).sort((a, b) => new Date(b.fecha_pago ?? b.created_at) - new Date(a.fecha_pago ?? a.created_at)) ?? []
);

const estadoMembresiaColor = (estado) => ({
    activa:  'success',
    vencida: 'error',
    pausada: 'warning',
}[estado] ?? 'secondary');

const estadoPagoColor = (estado) => ({
    pagado:   'success',
    vencido:  'error',
    pendiente:'warning',
}[estado] ?? 'secondary');
</script>

<template>
    <Head :title="`${client.nombre} ${client.apellido}`" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <v-btn
                icon="mdi-arrow-left"
                variant="text"
                color="white"
                @click="router.visit(route('clients.index'))"
            />
            <div>
                <h1 class="text-2xl font-bold text-white">
                    {{ client.nombre }} {{ client.apellido }}
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">Ficha del cliente</p>
            </div>
            <v-spacer />
            <v-btn
                color="warning"
                variant="tonal"
                prepend-icon="mdi-pencil"
                :href="route('clients.edit', client.id)"
            >
                Editar
            </v-btn>
        </div>

        <!-- Alerta de mora -->
        <v-alert
            v-if="overduePayments.length > 0"
            type="error"
            variant="tonal"
            class="mb-5"
            icon="mdi-alert-circle"
        >
            Este cliente tiene <strong>{{ overduePayments.length }} pago(s) vencido(s)</strong>. Gestionar en el módulo de cobranza.
        </v-alert>

        <v-row>
            <!-- Datos personales -->
            <v-col cols="12" md="4">
                <v-card color="#1f2937" rounded="lg" elevation="0" class="pa-5 mb-4">
                    <div class="flex items-center gap-3 mb-4">
                        <v-avatar color="primary" size="48">
                            <span class="text-white font-bold text-lg">
                                {{ client.nombre.charAt(0) }}
                            </span>
                        </v-avatar>
                        <div>
                            <div class="text-white font-semibold">{{ client.nombre }} {{ client.apellido }}</div>
                            <v-chip
                                :color="client.estado === 'activo' ? 'success' : 'secondary'"
                                size="x-small"
                                variant="tonal"
                            >
                                {{ client.estado === 'activo' ? 'Activo' : 'Inactivo' }}
                            </v-chip>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <v-icon size="16" color="gray">mdi-card-account-details</v-icon>
                            <span class="text-gray-400 text-sm">{{ client.cedula }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <v-icon size="16" color="gray">mdi-phone</v-icon>
                            <span class="text-gray-400 text-sm">{{ client.telefono }}</span>
                        </div>
                        <div v-if="client.email" class="flex items-center gap-2">
                            <v-icon size="16" color="gray">mdi-email</v-icon>
                            <span class="text-gray-400 text-sm">{{ client.email }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <v-icon size="16" color="gray">mdi-cake</v-icon>
                            <span class="text-gray-400 text-sm">{{ client.fecha_nacimiento }}</span>
                        </div>
                    </div>
                </v-card>

                <!-- Membresía activa -->
                <v-card color="#1f2937" rounded="lg" elevation="0" class="pa-5 mb-4">
                    <div class="text-gray-400 text-xs uppercase tracking-wide mb-3">Membresía actual</div>
                    <div v-if="activeMembership">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-white font-semibold">{{ activeMembership.plan?.nombre }}</span>
                            <v-chip :color="estadoMembresiaColor(activeMembership.estado)" size="x-small" variant="tonal">
                                {{ activeMembership.estado }}
                            </v-chip>
                        </div>
                        <div class="text-gray-400 text-sm">
                            <div>Inicio: {{ activeMembership.fecha_inicio }}</div>
                            <div>Vence: {{ activeMembership.fecha_fin }}</div>
                        </div>
                    </div>
                    <div v-else class="text-gray-500 text-sm">Sin membresía activa.</div>
                </v-card>

                <!-- Plan de entrenamiento -->
                <v-card color="#1f2937" rounded="lg" elevation="0" class="pa-5">
                    <div class="text-gray-400 text-xs uppercase tracking-wide mb-3">Plan de entrenamiento</div>
                    <div v-if="client.active_plan_assignment">
                        <div class="text-white font-semibold mb-1">
                            {{ client.active_plan_assignment.training_plan?.nombre }}
                        </div>
                        <div class="text-gray-400 text-sm">
                            Entrenador: {{ client.active_plan_assignment.trainer?.user?.name ?? '—' }}
                        </div>
                        <div class="text-gray-400 text-sm">
                            Días/semana: {{ client.active_plan_assignment.training_plan?.dias_semana }}
                        </div>
                    </div>
                    <div v-else class="text-gray-500 text-sm">Sin plan asignado.</div>
                </v-card>
            </v-col>

            <!-- Historial de pagos -->
            <v-col cols="12" md="8">
                <v-card color="#1f2937" rounded="lg" elevation="0">
                    <v-card-title class="pa-5 pb-0 text-white text-base font-semibold">
                        Historial de Pagos
                    </v-card-title>
                    <v-card-text class="pa-5 pt-3">
                        <v-data-table
                            v-if="allPayments.length > 0"
                            :headers="[
                                { title: 'Plan',        key: 'plan' },
                                { title: 'Monto',       key: 'monto' },
                                { title: 'Método',      key: 'metodo_pago' },
                                { title: 'Fecha pago',  key: 'fecha_pago' },
                                { title: 'Vencimiento', key: 'fecha_vencimiento' },
                                { title: 'Estado',      key: 'estado' },
                            ]"
                            :items="allPayments"
                            theme="dark"
                            density="comfortable"
                            hide-default-footer
                        >
                            <template #item.monto="{ item }">
                                <span class="font-medium">L. {{ Number(item.monto).toFixed(2) }}</span>
                            </template>

                            <template #item.metodo_pago="{ item }">
                                <div class="flex items-center gap-1">
                                    <v-icon size="14" :color="item.metodo_pago === 'efectivo' ? 'success' : 'info'">
                                        {{ item.metodo_pago === 'efectivo' ? 'mdi-cash' : 'mdi-credit-card' }}
                                    </v-icon>
                                    <span class="text-sm capitalize">{{ item.metodo_pago }}</span>
                                </div>
                            </template>

                            <template #item.estado="{ item }">
                                <v-chip
                                    :color="estadoPagoColor(item.estado)"
                                    size="small"
                                    variant="tonal"
                                >
                                    {{ item.estado }}
                                </v-chip>
                            </template>

                            <template #item.fecha_pago="{ item }">
                                <span class="text-gray-300 text-sm">{{ item.fecha_pago ?? '—' }}</span>
                            </template>

                            <template #item.fecha_vencimiento="{ item }">
                                <span class="text-gray-300 text-sm">{{ item.fecha_vencimiento }}</span>
                            </template>
                        </v-data-table>

                        <div v-else class="text-center py-8 text-gray-500">
                            <v-icon size="40" class="mb-2">mdi-receipt-text-off</v-icon>
                            <p>Sin historial de pagos.</p>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </AuthenticatedLayout>
</template>
