<script setup lang="ts">
import FlashMessage from '@/components/FlashMessage.vue';
import Icon from '@/components/Icon.vue';
import { cn } from '@/lib/utils';
import { dashboard, finance, logout } from '@/routes';
import { index as activityIndex } from '@/routes/activity';
import { update as appearanceUpdate } from '@/routes/appearance';
import { index as documentsIndex } from '@/routes/documents';
import { payroll } from '@/routes/hr';
import { index as employees } from '@/routes/hr/employees';
import { index as items } from '@/routes/inventory/items';
import { index as itemRequests } from '@/routes/inventory/item-requests';
import { index as purchases } from '@/routes/inventory/purchases';
import { index as stocks } from '@/routes/inventory/stocks';
import { index as suppliers } from '@/routes/inventory/suppliers';
import { requestApprovals } from '@/routes/inventory';
import { index as garage } from '@/routes/maintenance/garage';
import { index as onRoad } from '@/routes/maintenance/on-road';
import { dispatch, distribution } from '@/routes/operations';
import { index as drivers } from '@/routes/operations/drivers';
import { index as vehicles } from '@/routes/operations/vehicles';
import { edit as profileEdit } from '@/routes/profile';
import { update as sidebarUpdate } from '@/routes/sidebar';
import { index as usersIndex } from '@/routes/users';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

type NavLink = {
    type: 'link';
    label: string;
    href: string;
    match: string;
    icon: string;
};

type NavGroup = {
    type: 'group';
    key: string;
    label: string;
    icon: string;
    children: NavLink[];
};

type NavItem = NavLink | NavGroup;

withDefaults(
    defineProps<{
        title?: string;
    }>(),
    {
        title: 'Dashboard',
    },
);

const page = usePage();
const user = computed(() => page.props.auth.user);
const sidebarOpen = ref(page.props.sidebarOpen);
const mobileOpen = ref(false);
const openGroups = ref<Record<string, boolean>>({});

const navItems = computed((): NavItem[] => {
    const settingsChildren: NavLink[] = [];

    if (user.value?.is_admin) {
        settingsChildren.push(
            {
                type: 'link',
                label: 'Users',
                href: usersIndex.url(),
                match: '/users',
                icon: 'users',
            },
            {
                type: 'link',
                label: 'Activity',
                href: activityIndex.url(),
                match: '/activity',
                icon: 'activity',
            },
        );
    }

    settingsChildren.push({
        type: 'link',
        label: 'Profile',
        href: profileEdit.url(),
        match: '/profile',
        icon: 'user',
    });

    return [
        {
            type: 'link',
            label: 'Dashboard',
            href: dashboard.url(),
            match: '/dashboard',
            icon: 'home',
        },
        {
            type: 'group',
            key: 'operations',
            label: 'Operations',
            icon: 'clipboard',
            children: [
                {
                    type: 'link',
                    label: 'Vehicle',
                    href: vehicles.url(),
                    match: '/operations/vehicles',
                    icon: 'truck',
                },
                {
                    type: 'link',
                    label: 'Drivers',
                    href: drivers.url(),
                    match: '/operations/drivers',
                    icon: 'identification',
                },
                {
                    type: 'link',
                    label: 'Distribution',
                    href: distribution.url(),
                    match: '/operations/distribution',
                    icon: 'clipboard',
                },
                {
                    type: 'link',
                    label: 'Dispatch',
                    href: dispatch.url(),
                    match: '/operations/dispatch',
                    icon: 'paper-airplane',
                },
            ],
        },
        {
            type: 'link',
            label: 'Finance',
            href: finance.url(),
            match: '/finance',
            icon: 'banknotes',
        },
        {
            type: 'group',
            key: 'maintenance',
            label: 'Maintenance',
            icon: 'wrench',
            children: [
                {
                    type: 'link',
                    label: 'Garage',
                    href: garage.url(),
                    match: '/maintenance/garage',
                    icon: 'wrench',
                },
                {
                    type: 'link',
                    label: 'On-road',
                    href: onRoad.url(),
                    match: '/maintenance/on-road',
                    icon: 'map',
                },
            ],
        },
        {
            type: 'group',
            key: 'inventory',
            label: 'Inventory',
            icon: 'cube',
            children: [
                {
                    type: 'link',
                    label: 'Items',
                    href: items.url(),
                    match: '/inventory/items',
                    icon: 'cube',
                },
                {
                    type: 'link',
                    label: 'Store',
                    href: stocks.url(),
                    match: '/inventory/stocks',
                    icon: 'building-storefront',
                },
                {
                    type: 'link',
                    label: 'Purchase',
                    href: purchases.url(),
                    match: '/inventory/purchases',
                    icon: 'banknotes',
                },
                {
                    type: 'link',
                    label: 'Suppliers',
                    href: suppliers.url(),
                    match: '/inventory/suppliers',
                    icon: 'identification',
                },
                {
                    type: 'link',
                    label: 'Item request',
                    href: itemRequests.url(),
                    match: '/inventory/item-requests',
                    icon: 'clipboard',
                },
                {
                    type: 'link',
                    label: 'Request approval',
                    href: requestApprovals.url(),
                    match: '/inventory/request-approvals',
                    icon: 'check-badge',
                },
            ],
        },
        {
            type: 'group',
            key: 'hr',
            label: 'HR',
            icon: 'briefcase',
            children: [
                {
                    type: 'link',
                    label: 'Employees',
                    href: employees.url(),
                    match: '/hr/employees',
                    icon: 'identification',
                },
                {
                    type: 'link',
                    label: 'Payroll',
                    href: payroll.url(),
                    match: '/hr/payroll',
                    icon: 'calculator',
                },
            ],
        },
        {
            type: 'link',
            label: 'Documents',
            href: documentsIndex.url(),
            match: '/documents',
            icon: 'folder',
        },
        {
            type: 'group',
            key: 'settings',
            label: 'Settings',
            icon: 'cog',
            children: settingsChildren,
        },
    ];
});

const appearanceOptions = [
    { value: 'light', label: 'Light', icon: 'sun' },
    { value: 'dark', label: 'Dark', icon: 'moon' },
    { value: 'system', label: 'System', icon: 'computer' },
] as const;

const appearanceMenuOpen = ref(false);
const appearanceMenu = ref<HTMLElement | null>(null);

const currentAppearance = computed(
    () =>
        appearanceOptions.find(
            (option) => option.value === page.props.appearance,
        ) ?? appearanceOptions[2],
);

watch(
    () => page.props.appearance,
    (appearance) => applyAppearance(appearance),
    { immediate: true },
);

watch(
    () => [page.url, navItems.value] as const,
    ([, items]) => {
        items.forEach((item) => {
            if (item.type !== 'group') {
                return;
            }

            if (item.children.some((child) => isActive(child.match))) {
                openGroups.value[item.key] = true;
            }
        });
    },
    { immediate: true },
);

function applyAppearance(appearance: string): void {
    const prefersDark = window.matchMedia(
        '(prefers-color-scheme: dark)',
    ).matches;

    document.documentElement.classList.toggle(
        'dark',
        appearance === 'dark' || (appearance === 'system' && prefersDark),
    );
}

function persistSidebar(): void {
    router.patch(
        sidebarUpdate.url(),
        { open: sidebarOpen.value },
        { preserveState: true, preserveScroll: true },
    );
}

function toggleSidebar(): void {
    sidebarOpen.value = !sidebarOpen.value;
    persistSidebar();
}

function toggleGroup(key: string): void {
    if (!sidebarOpen.value) {
        sidebarOpen.value = true;
        openGroups.value[key] = true;
        persistSidebar();

        return;
    }

    openGroups.value[key] = !openGroups.value[key];
}

function isGroupOpen(group: NavGroup): boolean {
    return openGroups.value[group.key] === true;
}

function toggleAppearanceMenu(): void {
    appearanceMenuOpen.value = !appearanceMenuOpen.value;
}

function closeAppearanceMenu(): void {
    appearanceMenuOpen.value = false;
}

function setAppearance(appearance: 'light' | 'dark' | 'system'): void {
    closeAppearanceMenu();
    router.patch(
        appearanceUpdate.url(),
        { appearance },
        { preserveState: true, preserveScroll: true },
    );
}

function handleAppearanceMenuPointerDown(event: MouseEvent): void {
    if (!appearanceMenu.value?.contains(event.target as Node)) {
        closeAppearanceMenu();
    }
}

function handleAppearanceMenuKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        closeAppearanceMenu();
    }
}

onMounted(() => {
    document.addEventListener('mousedown', handleAppearanceMenuPointerDown);
    document.addEventListener('keydown', handleAppearanceMenuKeydown);
});

onUnmounted(() => {
    document.removeEventListener('mousedown', handleAppearanceMenuPointerDown);
    document.removeEventListener('keydown', handleAppearanceMenuKeydown);
});

function isActive(match: string): boolean {
    return (
        page.url === match ||
        page.url.startsWith(`${match}?`) ||
        page.url.startsWith(`${match}/`)
    );
}

function isGroupActive(group: NavGroup): boolean {
    return group.children.some((child) => isActive(child.match));
}

function linkClass(active: boolean): string {
    return cn(
        'flex items-center gap-3 rounded-lg py-2 text-sm font-medium',
        sidebarOpen.value ? 'px-3' : 'justify-center px-2',
        active
            ? 'bg-indigo-500 text-white'
            : 'text-slate-300 hover:bg-slate-800 hover:text-white',
    );
}
</script>

<template>
    <div
        class="min-h-screen bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-100"
    >
        <div
            v-if="mobileOpen"
            class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden"
            @click="mobileOpen = false"
        />

        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col border-r border-slate-800 bg-slate-900 text-slate-200 transition-all duration-200"
            :class="[
                sidebarOpen ? 'w-64' : 'w-20',
                mobileOpen
                    ? 'translate-x-0'
                    : '-translate-x-full lg:translate-x-0',
            ]"
        >
            <div class="flex h-16 items-center gap-3 px-4">
                <span
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-500 text-sm font-bold text-white"
                >
                    A
                </span>
                <div v-if="sidebarOpen" class="min-w-0">
                    <p class="truncate text-sm font-semibold text-white">
                        {{ page.props.name }}
                    </p>
                    <p class="text-xs text-slate-400">Admin panel</p>
                </div>
            </div>

            <nav class="flex flex-1 flex-col gap-1 overflow-y-auto p-3">
                <template v-for="item in navItems" :key="item.label">
                    <Link
                        v-if="item.type === 'link'"
                        :href="item.href"
                        prefetch
                        :title="item.label"
                        :class="linkClass(isActive(item.match))"
                        @click="mobileOpen = false"
                    >
                        <Icon :name="item.icon" />
                        <span v-if="sidebarOpen" class="truncate">{{
                            item.label
                        }}</span>
                    </Link>
                    <div v-else class="flex flex-col gap-1">
                        <button
                            type="button"
                            :title="item.label"
                            :class="
                                cn(
                                    linkClass(isGroupActive(item)),
                                    'w-full text-left',
                                )
                            "
                            :aria-expanded="isGroupOpen(item)"
                            @click="toggleGroup(item.key)"
                        >
                            <Icon :name="item.icon" />
                            <span v-if="sidebarOpen" class="min-w-0 flex-1 truncate">{{
                                item.label
                            }}</span>
                            <span
                                v-if="sidebarOpen"
                                class="transition-transform"
                                :class="isGroupOpen(item) ? 'rotate-180' : ''"
                            >
                                <Icon name="chevron-down" />
                            </span>
                        </button>
                        <div
                            v-if="isGroupOpen(item) && sidebarOpen"
                            class="ml-4 flex flex-col gap-1 border-l border-slate-700 pl-2"
                        >
                            <Link
                                v-for="child in item.children"
                                :key="child.href"
                                :href="child.href"
                                prefetch
                                :title="child.label"
                                :class="linkClass(isActive(child.match))"
                                @click="mobileOpen = false"
                            >
                                <Icon :name="child.icon" />
                                <span class="truncate">{{ child.label }}</span>
                            </Link>
                        </div>
                    </div>
                </template>
            </nav>

            <div class="border-t border-slate-800 p-3">
                <p
                    v-if="sidebarOpen"
                    class="mb-2 truncate px-1 text-xs text-slate-400"
                >
                    {{ user?.email }}
                </p>
                <Link
                    :href="logout.url()"
                    method="post"
                    as="button"
                    title="Sign out"
                    class="flex w-full items-center gap-3 rounded-lg py-2 text-sm text-slate-300 hover:bg-slate-800 hover:text-white"
                    :class="sidebarOpen ? 'px-3' : 'justify-center px-2'"
                >
                    <Icon name="logout" />
                    <span v-if="sidebarOpen">Sign out</span>
                </Link>
            </div>
        </aside>

        <div
            class="transition-[margin] duration-200"
            :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'"
        >
            <header
                class="sticky top-0 z-30 flex h-16 items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="rounded-lg p-2 hover:bg-slate-100 lg:hidden dark:hover:bg-slate-800"
                        aria-label="Open menu"
                        @click="mobileOpen = true"
                    >
                        <Icon name="menu" />
                    </button>
                    <button
                        type="button"
                        class="hidden rounded-lg p-2 hover:bg-slate-100 lg:inline-flex dark:hover:bg-slate-800"
                        :aria-label="
                            sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'
                        "
                        @click="toggleSidebar"
                    >
                        <Icon name="panel-left" />
                    </button>
                    <h1 class="text-base font-semibold">{{ title }}</h1>
                </div>

                <div class="flex items-center gap-2">
                    <div ref="appearanceMenu" class="relative">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-lg p-2 hover:bg-slate-100 dark:hover:bg-slate-800"
                            :class="
                                appearanceMenuOpen
                                    ? 'bg-slate-100 dark:bg-slate-800'
                                    : ''
                            "
                            :aria-expanded="appearanceMenuOpen"
                            aria-haspopup="menu"
                            aria-label="Theme"
                            :title="`${currentAppearance.label} theme`"
                            @click="toggleAppearanceMenu"
                        >
                            <Icon :name="currentAppearance.icon" />
                        </button>
                        <div
                            v-if="appearanceMenuOpen"
                            class="absolute right-0 z-30 mt-1 w-40 rounded-xl border border-slate-200 bg-white p-1 shadow-lg dark:border-slate-700 dark:bg-slate-900"
                            role="menu"
                            aria-label="Theme"
                        >
                            <button
                                v-for="option in appearanceOptions"
                                :key="option.value"
                                type="button"
                                class="flex w-full items-center gap-2 rounded-lg px-2.5 py-2 text-left text-sm"
                                :class="
                                    cn(
                                        page.props.appearance === option.value
                                            ? 'bg-indigo-600 text-white'
                                            : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800',
                                    )
                                "
                                role="menuitem"
                                @click="setAppearance(option.value)"
                            >
                                <Icon :name="option.icon" />
                                <span class="flex-1">{{ option.label }}</span>
                                <Icon
                                    v-if="
                                        page.props.appearance === option.value
                                    "
                                    name="check"
                                />
                            </button>
                        </div>
                    </div>
                    <span
                        class="hidden rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 sm:inline dark:bg-slate-800 dark:text-slate-300"
                    >
                        {{ user?.is_admin ? 'Administrator' : 'User' }}
                    </span>
                </div>
            </header>

            <main class="p-4 lg:p-8">
                <FlashMessage />
                <slot />
            </main>
        </div>
    </div>
</template>
