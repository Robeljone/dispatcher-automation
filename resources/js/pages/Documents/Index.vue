<script setup lang="ts">
import FolderTree from '@/components/FolderTree.vue';
import Icon from '@/components/Icon.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy as destroyFile, download, store as storeFile } from '@/actions/App/Http/Controllers/DocumentFileController';
import {
    destroy as destroyFolder,
    store as storeFolder,
    update as updateFolder,
} from '@/actions/App/Http/Controllers/DocumentFolderController';
import { index as documentsIndex } from '@/routes/documents';
import { Form, Head, Link, router, setLayoutProps, useForm, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

setLayoutProps({
    title: 'Documents',
});

defineOptions({
    layout: AppLayout,
});

type FolderNode = {
    id: number;
    name: string;
    children: FolderNode[];
};

type ExplorerItem = {
    id: number;
    name: string;
    kind: 'folder' | 'image' | 'pdf' | 'document';
    size?: string;
    uploader?: string | null;
    location?: string | null;
    updated_at: string | null;
};

const props = defineProps<{
    folder: { id: number; name: string; parent_id: number | null } | null;
    breadcrumbs: { id: number; name: string }[];
    tree: FolderNode[];
    search: string;
    items: ExplorerItem[];
}>();

const page = usePage();
const errors = computed(() => {
    const value = page.props.errors;

    return value && typeof value === 'object'
        ? (value as Record<string, string>)
        : {};
});
const selectedId = ref<string | null>(null);
const showNewFolder = ref(false);
const rootExpanded = ref(true);
const fileInput = ref<HTMLInputElement | null>(null);
const search = ref(props.search);
const isSearching = computed(() => search.value.trim() !== '');
let searchTimer: ReturnType<typeof setTimeout> | null = null;
const uploadForm = useForm({
    folder_id: null as number | null,
    file: null as File | null,
});

const currentPath = computed(() => {
    const names = props.breadcrumbs.map((crumb) => crumb.name);

    return names.length ? `Company Documents \\ ${names.join(' \\ ')}` : 'Company Documents';
});

const fileError = computed(() => uploadForm.errors.file || errors.value.file);
const uploadPercent = computed(() => uploadForm.progress?.percentage ?? 0);

function itemKey(item: ExplorerItem): string {
    return `${item.kind}-${item.id}`;
}

function openFolder(id: number): void {
    router.get(documentsIndex.url({ query: { folder: id } }));
}

function goRoot(): void {
    router.get(documentsIndex.url());
}

function applySearch(value: string): void {
    const trimmed = value.trim();
    const query: Record<string, string | number> = {};

    if (trimmed !== '') {
        query.search = trimmed;
    } else if (props.folder) {
        query.folder = props.folder.id;
    }

    router.get(documentsIndex.url({ query }), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['items', 'search', 'folder', 'breadcrumbs'],
    });
}

watch(search, (value) => {
    if (searchTimer) {
        clearTimeout(searchTimer);
    }

    searchTimer = setTimeout(() => {
        applySearch(value);
    }, 300);
});

onBeforeUnmount(() => {
    if (searchTimer) {
        clearTimeout(searchTimer);
    }
});

function openItem(item: ExplorerItem): void {
    if (item.kind === 'folder') {
        openFolder(item.id);

        return;
    }

    window.location.assign(download.url(item.id));
}

function selectItem(item: ExplorerItem): void {
    selectedId.value = itemKey(item);
}

function selectedItem(): ExplorerItem | undefined {
    return props.items.find((item) => itemKey(item) === selectedId.value);
}

function confirmDelete(): void {
    const item = selectedItem();

    if (!item) {
        return;
    }

    const confirmed = window.confirm(`Delete "${item.name}"?`);

    if (!confirmed) {
        return;
    }

    if (item.kind === 'folder') {
        router.delete(destroyFolder.url(item.id));

        return;
    }

    router.delete(destroyFile.url(item.id));
}

function renameFolder(): void {
    const item = selectedItem();

    if (!item || item.kind !== 'folder') {
        return;
    }

    const name = window.prompt('Rename folder', item.name)?.trim();

    if (!name || name === item.name) {
        return;
    }

    router.patch(updateFolder.url(item.id), { name });
}

function iconFor(kind: ExplorerItem['kind']): string {
    if (kind === 'folder') {
        return 'folder';
    }

    if (kind === 'image') {
        return 'photo';
    }

    if (kind === 'pdf') {
        return 'document';
    }

    return 'document-text';
}

function pickFile(): void {
    if (uploadForm.processing) {
        return;
    }

    fileInput.value?.click();
}

function uploadSelectedFile(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    if (!file) {
        return;
    }

    uploadForm.folder_id = props.folder?.id ?? null;
    uploadForm.file = file;
    uploadForm.post(storeFile.url(), {
        forceFormData: true,
        onFinish: () => {
            uploadForm.reset();
            input.value = '';
        },
    });
}
</script>

<template>
    <Head title="Documents" />
    <section
        class="relative flex h-[calc(100vh-8rem)] flex-col overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
    >
        <div
            v-if="uploadForm.processing"
            class="absolute inset-0 z-20 flex flex-col items-center justify-center gap-3 bg-white/80 dark:bg-slate-950/80"
            role="status"
            aria-live="polite"
        >
            <span
                class="h-10 w-10 animate-spin rounded-full border-4 border-indigo-200 border-t-indigo-600"
            />
            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">
                Uploading{{ uploadForm.file ? ` ${uploadForm.file.name}` : '' }}…
            </p>
            <div class="h-2 w-56 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                <div
                    class="h-full bg-indigo-600 transition-[width]"
                    :style="{ width: `${uploadPercent}%` }"
                />
            </div>
            <p class="text-xs text-slate-500">{{ uploadPercent }}%</p>
        </div>

        <div
            class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-slate-100 px-3 py-2 dark:border-slate-700 dark:bg-slate-800"
        >
            <button
                type="button"
                class="rounded-md bg-white px-3 py-1.5 text-sm font-medium shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-700"
                @click="showNewFolder = !showNewFolder"
            >
                New folder
            </button>
            <button
                type="button"
                class="rounded-md bg-white px-3 py-1.5 text-sm font-medium shadow-sm ring-1 ring-slate-200 disabled:opacity-50 dark:bg-slate-900 dark:ring-slate-700"
                :disabled="uploadForm.processing"
                @click="pickFile"
            >
                {{ uploadForm.processing ? 'Uploading…' : 'Upload' }}
            </button>
            <button
                type="button"
                class="rounded-md px-3 py-1.5 text-sm font-medium hover:bg-slate-200 disabled:opacity-40 dark:hover:bg-slate-700"
                :disabled="!selectedItem() || selectedItem()?.kind !== 'folder'"
                @click="renameFolder"
            >
                Rename
            </button>
            <button
                type="button"
                class="rounded-md px-3 py-1.5 text-sm font-medium text-rose-700 hover:bg-rose-50 disabled:opacity-40 dark:hover:bg-rose-950"
                :disabled="!selectedItem()"
                @click="confirmDelete"
            >
                Delete
            </button>
            <input
                v-model="search"
                type="search"
                placeholder="Search folders, files, and extensions"
                class="ml-auto w-full max-w-xs rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm dark:border-slate-600 dark:bg-slate-950"
            />
        </div>

        <div
            class="flex items-center gap-2 border-b border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"
        >
            <button
                type="button"
                class="rounded px-2 py-1 hover:bg-slate-100 dark:hover:bg-slate-800"
                @click="goRoot"
            >
                Company Documents
            </button>
            <template v-for="crumb in breadcrumbs" :key="crumb.id">
                <span class="text-slate-400">\</span>
                <Link
                    :href="documentsIndex.url({ query: { folder: crumb.id } })"
                    class="rounded px-2 py-1 hover:bg-slate-100 dark:hover:bg-slate-800"
                >
                    {{ crumb.name }}
                </Link>
            </template>
            <span class="ml-auto truncate text-xs text-slate-400">{{
                isSearching ? 'Search results' : currentPath
            }}</span>
        </div>

        <Form
            v-if="showNewFolder"
            v-bind="storeFolder.form()"
            class="flex items-center gap-2 border-b border-slate-200 px-3 py-2 dark:border-slate-700"
            #default="{ processing }"
            @success="showNewFolder = false"
        >
            <input
                v-if="folder"
                type="hidden"
                name="parent_id"
                :value="folder.id"
            />
            <input
                name="name"
                required
                maxlength="100"
                placeholder="New folder name"
                class="w-64 rounded-md border border-slate-300 px-3 py-1.5 text-sm dark:border-slate-600 dark:bg-slate-950"
            />
            <button
                type="submit"
                class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white disabled:opacity-60"
                :disabled="processing"
            >
                Create
            </button>
        </Form>

        <input
            ref="fileInput"
            type="file"
            class="sr-only"
            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp,application/pdf,image/*"
            :disabled="uploadForm.processing"
            @change="uploadSelectedFile"
        />

        <div v-if="fileError" class="px-3 py-2 text-sm text-rose-600">
            {{ fileError }}
        </div>
        <div v-if="errors.name" class="px-3 py-2 text-sm text-rose-600">
            {{ errors.name }}
        </div>

        <div class="flex min-h-0 flex-1">
            <aside
                class="w-56 shrink-0 overflow-y-auto border-r border-slate-200 bg-slate-50 p-2 text-sm dark:border-slate-700 dark:bg-slate-950"
            >
                <div class="flex items-center gap-0.5">
                    <button
                        v-if="tree.length"
                        type="button"
                        class="rounded p-0.5 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-800"
                        :aria-expanded="rootExpanded"
                        :aria-label="
                            rootExpanded
                                ? 'Collapse Company Documents'
                                : 'Expand Company Documents'
                        "
                        @click="rootExpanded = !rootExpanded"
                    >
                        <Icon
                            name="chevron-down"
                            class="transition-transform"
                            :class="rootExpanded ? '' : '-rotate-90'"
                        />
                    </button>
                    <span v-else class="inline-block w-6 shrink-0" />
                    <button
                        type="button"
                        class="flex min-w-0 flex-1 items-center gap-2 rounded px-2 py-1.5 text-left"
                        :class="
                            folder === null
                                ? 'bg-sky-100 dark:bg-sky-900/40'
                                : 'hover:bg-slate-200 dark:hover:bg-slate-800'
                        "
                        @click="goRoot"
                    >
                        <Icon name="folder" />
                        Company Documents
                    </button>
                </div>
                <div v-if="rootExpanded" class="mt-1 ml-3">
                    <FolderTree
                        :nodes="tree"
                        :current-id="folder?.id ?? null"
                        @open="openFolder"
                    />
                </div>
            </aside>

            <div
                class="min-w-0 flex-1 overflow-y-auto bg-white p-4 dark:bg-slate-900"
                @click="selectedId = null"
            >
                <p v-if="!items.length" class="pt-16 text-center text-sm text-slate-500">
                    {{
                        isSearching
                            ? 'No folders or files match that search.'
                            : 'This folder is empty. Create a folder or upload a company document.'
                    }}
                </p>
                <ul v-else class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6">
                    <li
                        v-for="item in items"
                        :key="itemKey(item)"
                        class="flex cursor-default flex-col items-center rounded-lg p-3 text-center"
                        :class="
                            selectedId === itemKey(item)
                                ? 'bg-sky-100 ring-1 ring-sky-300 dark:bg-sky-900/40 dark:ring-sky-700'
                                : 'hover:bg-slate-100 dark:hover:bg-slate-800'
                        "
                        @click.stop="selectItem(item)"
                        @dblclick="openItem(item)"
                    >
                        <span
                            class="flex h-14 w-14 items-center justify-center rounded-lg"
                            :class="
                                item.kind === 'folder'
                                    ? 'bg-amber-300 text-amber-900'
                                    : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-100'
                            "
                        >
                            <Icon :name="iconFor(item.kind)" />
                        </span>
                        <span class="mt-2 w-full truncate text-sm">{{ item.name }}</span>
                        <span v-if="item.location" class="w-full truncate text-[11px] text-slate-400">{{
                            item.location
                        }}</span>
                        <span v-if="item.size" class="text-xs text-slate-400">{{
                            item.size
                        }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div
            class="border-t border-slate-200 px-3 py-1.5 text-xs text-slate-500 dark:border-slate-700"
        >
            {{ items.length }} item{{ items.length === 1 ? '' : 's' }}
            <span v-if="isSearching"> matching “{{ search.trim() }}”</span>
            <span v-if="selectedItem()"> · {{ selectedItem()?.name }}</span>
        </div>
    </section>
</template>
