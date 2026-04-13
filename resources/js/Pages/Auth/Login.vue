<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email:    '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar Sesión" />

    <v-app theme="dark">

        <div
            class="min-h-screen flex items-center justify-center"
            style="background: radial-gradient(ellipse at 60% 40%, #0d2818 0%, #0a0a0a 70%)"
        >
            <v-card
                color="#111827"
                rounded="xl"
                elevation="0"
                width="420"
                style="border: 1px solid #1e293b"
                class="pa-8"
            >
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4"
                        style="background: #22c55e18"
                    >
                        <v-icon color="primary" size="32">mdi-dumbbell</v-icon>
                    </div>
                    <h1 class="text-white text-xl font-bold">Sistema de Gestión</h1>
                    <p class="text-sm mt-1" style="color: #64748b">Gimnasio · Acceso exclusivo empleados</p>
                </div>

                <!-- Status -->
                <v-alert
                    v-if="status"
                    type="success"
                    variant="tonal"
                    density="compact"
                    class="mb-5"
                >
                    {{ status }}
                </v-alert>

                <!-- Form -->
                <div class="space-y-4">
                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: #94a3b8">
                            Correo electrónico
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            autofocus
                            autocomplete="username"
                            class="w-full px-4 py-3 rounded-lg text-sm text-white placeholder-slate-500 outline-none transition-all"
                            style="background: #0f172a; border: 1px solid #1e293b; color: #f1f5f9"
                            @focus="$event.target.style.borderColor='#22c55e'"
                            @blur="$event.target.style.borderColor='#1e293b'"
                        />
                        <p v-if="form.errors.email" class="text-red-400 text-xs mt-1.5">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: #94a3b8">
                            Contraseña
                        </label>
                        <div class="relative">
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="current-password"
                                class="w-full px-4 py-3 rounded-lg text-sm outline-none transition-all pr-11"
                                style="background: #0f172a; border: 1px solid #1e293b; color: #f1f5f9"
                                @focus="$event.target.style.borderColor='#22c55e'"
                                @blur="$event.target.style.borderColor='#1e293b'"
                                @keyup.enter="submit"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2"
                                style="color: #475569"
                                @click="showPassword = !showPassword"
                            >
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="text-red-400 text-xs mt-1.5">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Botón -->
                    <button
                        type="button"
                        :disabled="form.processing"
                        class="w-full py-3 rounded-lg text-sm font-semibold text-white transition-all mt-2"
                        style="background: #22c55e; letter-spacing: 0.02em"
                        @click="submit"
                        @mouseenter="$event.target.style.background='#16a34a'"
                        @mouseleave="$event.target.style.background='#22c55e'"
                    >
                        <span v-if="!form.processing">Ingresar al Sistema</span>
                        <span v-else class="flex items-center justify-center gap-2">
                            <v-progress-circular size="16" width="2" indeterminate color="white" />
                            Verificando...
                        </span>
                    </button>
                </div>

                <!-- Footer -->
                <p class="text-center text-xs mt-6" style="color: #334155">
                    Acceso restringido · Solo personal autorizado
                </p>
            </v-card>
        </div>
    </v-app>
</template>
