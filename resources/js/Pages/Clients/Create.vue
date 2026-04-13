<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ plans: Array });

const step = ref(1);
const totalSteps = 4;

const form = useForm({
    nombre:              '',
    apellido:            '',
    telefono:            '',
    email:               '',
    cedula:              '',
    fecha_nacimiento:    '',
    membership_plan_id:  null,
    metodo_pago:         '',
});

const selectedPlan = computed(() =>
    props.plans.find(p => p.id === form.membership_plan_id) ?? null
);

const stepValid = computed(() => {
    if (step.value === 1)
        return form.nombre && form.apellido && form.telefono && form.cedula && form.fecha_nacimiento;
    if (step.value === 2)
        return form.membership_plan_id !== null;
    if (step.value === 3)
        return form.metodo_pago !== '';
    return true;
});

const next = () => { if (stepValid.value && step.value < totalSteps) step.value++; };
const back = () => { if (step.value > 1) step.value--; };

const submit = () => {
    form.post(route('clients.store'), {
        onSuccess: () => form.reset(),
    });
};

const planTypeLabel = (tipo) => ({
    mensual:   'Mensual',
    semestral: '6 Meses',
    anual:     'Anual',
}[tipo] ?? tipo);
</script>

<template>
    <Head title="Registrar Cliente" />

    <AuthenticatedLayout>
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-white">Registrar Nuevo Cliente</h1>
                <p class="text-gray-400 text-sm mt-1">Complete los pasos para dar de alta al cliente.</p>
            </div>

            <!-- Stepper -->
            <v-stepper
                v-model="step"
                :items="['Datos personales', 'Plan de membresía', 'Pago inicial', 'Confirmación']"
                hide-actions
                color="primary"
                bg-color="#111827"
                class="mb-6"
            />

            <!-- Card contenedor -->
            <v-card color="#1f2937" rounded="lg" elevation="0" class="pa-6">

                <!-- PASO 1 — Datos personales -->
                <div v-if="step === 1">
                    <h2 class="text-white font-semibold text-base mb-4">Datos Personales</h2>
                    <v-row>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.nombre"
                                label="Nombre *"
                                variant="outlined"
                                density="comfortable"
                                :error-messages="form.errors.nombre"
                                color="primary"
                            />
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.apellido"
                                label="Apellido *"
                                variant="outlined"
                                density="comfortable"
                                :error-messages="form.errors.apellido"
                                color="primary"
                            />
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.telefono"
                                label="Teléfono *"
                                variant="outlined"
                                density="comfortable"
                                :error-messages="form.errors.telefono"
                                color="primary"
                            />
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.cedula"
                                label="Cédula *"
                                variant="outlined"
                                density="comfortable"
                                :error-messages="form.errors.cedula"
                                color="primary"
                            />
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.fecha_nacimiento"
                                label="Fecha de nacimiento *"
                                type="date"
                                variant="outlined"
                                density="comfortable"
                                :error-messages="form.errors.fecha_nacimiento"
                                color="primary"
                            />
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.email"
                                label="Correo electrónico"
                                type="email"
                                variant="outlined"
                                density="comfortable"
                                :error-messages="form.errors.email"
                                color="primary"
                            />
                        </v-col>
                    </v-row>
                </div>

                <!-- PASO 2 — Plan de membresía -->
                <div v-if="step === 2">
                    <h2 class="text-white font-semibold text-base mb-4">Seleccionar Plan de Membresía</h2>
                    <v-row>
                        <v-col
                            v-for="plan in plans"
                            :key="plan.id"
                            cols="12" sm="4"
                        >
                            <v-card
                                :color="form.membership_plan_id === plan.id ? 'primary' : '#111827'"
                                rounded="lg"
                                elevation="0"
                                class="cursor-pointer transition-all border-2"
                                :class="form.membership_plan_id === plan.id ? 'border-green-400' : 'border-gray-700'"
                                @click="form.membership_plan_id = plan.id"
                            >
                                <v-card-text class="pa-5 text-center">
                                    <v-icon
                                        :color="form.membership_plan_id === plan.id ? 'white' : 'primary'"
                                        size="36"
                                        class="mb-2"
                                    >
                                        mdi-card-account-details
                                    </v-icon>
                                    <div class="font-bold text-white text-base">{{ plan.nombre }}</div>
                                    <div class="text-xs mt-1 mb-3"
                                         :class="form.membership_plan_id === plan.id ? 'text-green-100' : 'text-gray-400'"
                                    >
                                        {{ planTypeLabel(plan.tipo) }} · {{ plan.duracion_dias }} días
                                    </div>
                                    <div class="text-2xl font-bold"
                                         :class="form.membership_plan_id === plan.id ? 'text-white' : 'text-green-400'"
                                    >
                                        L. {{ Number(plan.precio).toFixed(2) }}
                                    </div>
                                    <p class="text-xs mt-2"
                                       :class="form.membership_plan_id === plan.id ? 'text-green-100' : 'text-gray-500'"
                                    >
                                        {{ plan.descripcion }}
                                    </p>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                    <p v-if="form.errors.membership_plan_id" class="text-red-400 text-sm mt-2">
                        {{ form.errors.membership_plan_id }}
                    </p>
                </div>

                <!-- PASO 3 — Método de pago -->
                <div v-if="step === 3">
                    <h2 class="text-white font-semibold text-base mb-2">Cobro Inicial</h2>

                    <v-alert type="info" variant="tonal" class="mb-5" density="compact">
                        Plan seleccionado: <strong>{{ selectedPlan?.nombre }}</strong> —
                        Monto a cobrar: <strong>L. {{ Number(selectedPlan?.precio ?? 0).toFixed(2) }}</strong>
                    </v-alert>

                    <p class="text-gray-400 text-sm mb-4">Seleccione el método de pago:</p>

                    <v-row>
                        <v-col cols="12" sm="6">
                            <v-card
                                :color="form.metodo_pago === 'efectivo' ? 'primary' : '#111827'"
                                rounded="lg"
                                elevation="0"
                                class="cursor-pointer border-2"
                                :class="form.metodo_pago === 'efectivo' ? 'border-green-400' : 'border-gray-700'"
                                @click="form.metodo_pago = 'efectivo'"
                            >
                                <v-card-text class="pa-5 text-center">
                                    <v-icon size="40" :color="form.metodo_pago === 'efectivo' ? 'white' : 'success'" class="mb-2">
                                        mdi-cash
                                    </v-icon>
                                    <div class="text-white font-semibold">Efectivo</div>
                                </v-card-text>
                            </v-card>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-card
                                :color="form.metodo_pago === 'tarjeta' ? 'primary' : '#111827'"
                                rounded="lg"
                                elevation="0"
                                class="cursor-pointer border-2"
                                :class="form.metodo_pago === 'tarjeta' ? 'border-green-400' : 'border-gray-700'"
                                @click="form.metodo_pago = 'tarjeta'"
                            >
                                <v-card-text class="pa-5 text-center">
                                    <v-icon size="40" :color="form.metodo_pago === 'tarjeta' ? 'white' : 'info'" class="mb-2">
                                        mdi-credit-card
                                    </v-icon>
                                    <div class="text-white font-semibold">Tarjeta</div>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                    <p v-if="form.errors.metodo_pago" class="text-red-400 text-sm mt-2">
                        {{ form.errors.metodo_pago }}
                    </p>
                </div>

                <!-- PASO 4 — Confirmación -->
                <div v-if="step === 4">
                    <h2 class="text-white font-semibold text-base mb-4">Confirmar Registro</h2>
                    <p class="text-gray-400 text-sm mb-5">Revise los datos antes de guardar.</p>

                    <v-row>
                        <v-col cols="12" sm="6">
                            <v-card color="#111827" rounded="lg" elevation="0" class="pa-4">
                                <div class="text-gray-400 text-xs uppercase tracking-wide mb-3">Datos personales</div>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400 text-sm">Nombre</span>
                                        <span class="text-white text-sm font-medium">{{ form.nombre }} {{ form.apellido }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400 text-sm">Teléfono</span>
                                        <span class="text-white text-sm">{{ form.telefono }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400 text-sm">Cédula</span>
                                        <span class="text-white text-sm">{{ form.cedula }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400 text-sm">Nacimiento</span>
                                        <span class="text-white text-sm">{{ form.fecha_nacimiento }}</span>
                                    </div>
                                    <div v-if="form.email" class="flex justify-between">
                                        <span class="text-gray-400 text-sm">Correo</span>
                                        <span class="text-white text-sm">{{ form.email }}</span>
                                    </div>
                                </div>
                            </v-card>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-card color="#111827" rounded="lg" elevation="0" class="pa-4">
                                <div class="text-gray-400 text-xs uppercase tracking-wide mb-3">Membresía y pago</div>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400 text-sm">Plan</span>
                                        <span class="text-white text-sm font-medium">{{ selectedPlan?.nombre }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400 text-sm">Duración</span>
                                        <span class="text-white text-sm">{{ selectedPlan?.duracion_dias }} días</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400 text-sm">Monto</span>
                                        <span class="text-green-400 text-sm font-bold">L. {{ Number(selectedPlan?.precio ?? 0).toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400 text-sm">Método de pago</span>
                                        <span class="text-white text-sm capitalize">{{ form.metodo_pago }}</span>
                                    </div>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </div>

                <!-- Botones de navegación -->
                <div class="flex justify-between mt-6">
                    <v-btn
                        v-if="step > 1"
                        variant="tonal"
                        color="secondary"
                        prepend-icon="mdi-arrow-left"
                        @click="back"
                    >
                        Anterior
                    </v-btn>
                    <div v-else />

                    <v-btn
                        v-if="step < totalSteps"
                        color="primary"
                        append-icon="mdi-arrow-right"
                        :disabled="!stepValid"
                        @click="next"
                    >
                        Siguiente
                    </v-btn>

                    <v-btn
                        v-if="step === totalSteps"
                        color="success"
                        prepend-icon="mdi-check"
                        :loading="form.processing"
                        @click="submit"
                    >
                        Confirmar Registro
                    </v-btn>
                </div>
            </v-card>
        </div>
    </AuthenticatedLayout>
</template>
