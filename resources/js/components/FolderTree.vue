<script setup lang="ts">
import FolderTree from '@/components/FolderTree.vue';
import Icon from '@/components/Icon.vue';
import { reactive } from 'vue';

export type FolderNode = {
    id: number;
    name: string;
    children: FolderNode[];
};

defineOptions({
    name: 'FolderTree',
});

defineProps<{
    nodes: FolderNode[];
    currentId: number | null;
}>();

const emit = defineEmits<{
    open: [id: number];
}>();

const expanded = reactive<Record<number, boolean>>({});

function isExpanded(node: FolderNode): boolean {
    return expanded[node.id] ?? true;
}

function toggle(id: number): void {
    expanded[id] = !(expanded[id] ?? true);
}
</script>

<template>
    <div class="flex flex-col gap-0.5">
        <div v-for="node in nodes" :key="node.id">
            <div class="flex items-center gap-0.5">
                <button
                    v-if="node.children.length"
                    type="button"
                    class="rounded p-0.5 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-800"
                    :aria-expanded="isExpanded(node)"
                    :aria-label="
                        isExpanded(node)
                            ? `Collapse ${node.name}`
                            : `Expand ${node.name}`
                    "
                    @click.stop="toggle(node.id)"
                >
                    <Icon
                        name="chevron-down"
                        class="transition-transform"
                        :class="isExpanded(node) ? '' : '-rotate-90'"
                    />
                </button>
                <span v-else class="inline-block w-6 shrink-0" />
                <button
                    type="button"
                    class="flex min-w-0 flex-1 items-center gap-2 rounded px-2 py-1 text-left"
                    :class="
                        currentId === node.id
                            ? 'bg-sky-100 dark:bg-sky-900/40'
                            : 'hover:bg-slate-200 dark:hover:bg-slate-800'
                    "
                    @click="emit('open', node.id)"
                >
                    <Icon name="folder" />
                    <span class="truncate">{{ node.name }}</span>
                </button>
            </div>
            <div
                v-if="node.children.length && isExpanded(node)"
                class="ml-4 mt-0.5"
            >
                <FolderTree
                    :nodes="node.children"
                    :current-id="currentId"
                    @open="emit('open', $event)"
                />
            </div>
        </div>
    </div>
</template>
