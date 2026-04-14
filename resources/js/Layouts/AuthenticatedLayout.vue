<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isAdmin = computed(() => user.value.role === 'admin');
const sidebarOpen = ref(true);

const navItems = computed(() => {
    const items = [
        { label: 'Panel General',           icon: 'mdi-view-dashboard-outline', route: 'dashboard',              roles: ['admin', 'receptionist', 'trainer'] },
        { label: 'Nuevo Cliente',           icon: 'mdi-account-plus-outline',   route: 'clients.create',         roles: ['admin', 'receptionist'] },
        { label: 'Consulta de Clientes',    icon: 'mdi-account-search-outline', route: 'clients.index',          roles: ['admin', 'receptionist'] },
        { label: 'Cobranza',                icon: 'mdi-cash-register',          route: 'payments.index',         roles: ['admin', 'receptionist'] },
        { label: 'Planes de Entrenamiento', icon: 'mdi-dumbbell',               route: 'training-plans.index',   roles: ['admin', 'trainer'] },
        { label: 'Asignaciones',            icon: 'mdi-clipboard-account',      route: 'assignments.index',      roles: ['admin', 'trainer'] },
        { label: 'Reportes Financieros',    icon: 'mdi-chart-bar',              route: 'admin.finances.index',   roles: ['admin'] },
        { label: 'Gestión Empleados',       icon: 'mdi-account-group-outline',  route: 'admin.users.index',      roles: ['admin'] },
        { label: 'Directorio Soporte',      icon: 'mdi-wrench-outline',         route: 'support-providers.index',roles: ['admin', 'receptionist'] },
    ];
    return items.filter(item => item.roles.includes(user.value.role));
});

const roleLabel = computed(() => ({
    admin:        'Administrador',
    receptionist: 'Recepcionista',
    trainer:      'Entrenador',
}[user.value.role] ?? user.value.role));

const roleColor = computed(() => ({
    admin:        'warning',
    receptionist: 'success',
    trainer:      'info',
}[user.value.role] ?? 'secondary'));
</script>

<template>
    <v-app theme="dark">
        <!-- Sidebar -->
        <v-navigation-drawer
            v-model="sidebarOpen"
            permanent
            :width="248"
            color="#0f172a"
            border="none"
        >
            <!-- Logo -->
            <div class="px-5 py-5" style="border-bottom: 1px solid #1e293b">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg" style="background: #22c55e20">
                        <v-icon color="primary" size="20">mdi-dumbbell</v-icon>
                    </div>
                    <div>
                        <div class="text-white font-semibold text-sm leading-tight">Gimnasio</div>
                        <div class="text-xs" style="color: #64748b">Sistema de Gestión</div>
                    </div>
                </div>
            </div>

            <!-- Nav Items -->
            <div class="px-3 py-3">
                <template v-for="item in navItems" :key="item.route">
                    <a
                        :href="route(item.route)"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-0.5 transition-all cursor-pointer no-underline"
                        :class="route().current(item.route)
                            ? 'bg-green-500/10 text-green-400'
                            : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200'"
                    >
                        <v-icon :icon="item.icon" size="18" />
                        <span class="text-sm font-medium">{{ item.label }}</span>
                        <div
                            v-if="route().current(item.route)"
                            class="ml-auto w-1.5 h-1.5 rounded-full bg-green-400"
                        />
                    </a>
                </template>
            </div>

            <!-- User info -->
            <template #append>
                <div class="px-4 py-4" style="border-top: 1px solid #1e293b">
                    <div class="flex items-center gap-3 mb-3">
                        <v-avatar
                            size="34"
                            class="font-bold text-white text-sm"
                            :style="`background: #22c55e30; color: #22c55e`"
                        >
                            {{ user.name.charAt(0).toUpperCase() }}
                        </v-avatar>
                        <div class="flex-1 min-w-0">
                            <div class="text-white text-xs font-semibold truncate">{{ user.name }}</div>
                            <v-chip
                                :color="roleColor"
                                size="x-small"
                                variant="tonal"
                                class="mt-0.5"
                            >
                                {{ roleLabel }}
                            </v-chip>
                        </div>
                    </div>
                    <Link :href="route('logout')" method="post" as="button" class="w-full">
                        <v-btn
                            variant="tonal"
                            color="error"
                            size="small"
                            block
                            prepend-icon="mdi-logout"
                            style="text-transform: none"
                        >
                            Cerrar Sesión
                        </v-btn>
                    </Link>
                </div>
            </template>
        </v-navigation-drawer>

        <!-- Main Content -->
        <v-main style="background: #0f172a">
            <div class="pa-7">
                <slot />
            </div>
        </v-main>

        <!-- Flash notifications -->
        <FlashMessage />
    </v-app>
</template>
