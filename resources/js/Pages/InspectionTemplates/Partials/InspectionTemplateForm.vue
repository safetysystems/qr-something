<script setup>
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCheckbox from '@/Components/Base/BaseCheckbox.vue';
import BaseFieldError from '@/Components/Base/BaseFieldError.vue';
import BaseInput from '@/Components/Base/BaseInput.vue';
import BaseLabel from '@/Components/Base/BaseLabel.vue';
import BaseTextarea from '@/Components/Base/BaseTextarea.vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    locked: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['add-item', 'remove-item']);

function errorFor(index, field) {
    return props.form.errors[`items.${index}.${field}`];
}
</script>

<template>
    <div class="grid gap-6 lg:grid-cols-2">
        <div>
            <BaseLabel for-id="name">Template name</BaseLabel>
            <BaseInput
                id="name"
                v-model="form.name"
                name="name"
                required
                :disabled="locked"
            />
            <BaseFieldError :message="form.errors.name" />
        </div>

        <div class="lg:col-span-2">
            <BaseLabel for-id="description">Description</BaseLabel>
            <BaseTextarea
                id="description"
                v-model="form.description"
                name="description"
                placeholder="Briefly explain when this checklist should be used."
                :disabled="locked"
            />
            <BaseFieldError :message="form.errors.description" />
        </div>
    </div>

    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold">Checklist items</h2>
                <p class="page-subtitle mt-1">Keep items short and operational so inspections stay consistent.</p>
            </div>

            <BaseButton
                v-if="!locked"
                type="button"
                variant="secondary"
                @click="emit('add-item')"
            >
                Add item
            </BaseButton>
        </div>

        <BaseFieldError :message="form.errors.items" />

        <div class="space-y-4">
            <div
                v-for="(item, index) in form.items"
                :key="index"
                class="card card-muted"
            >
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold">Item {{ index + 1 }}</p>
                        <p class="page-subtitle mt-1">Define the field the inspector must evaluate.</p>
                    </div>

                    <BaseButton
                        v-if="!locked && form.items.length > 1"
                        type="button"
                        variant="ghost"
                        @click="emit('remove-item', index)"
                    >
                        Remove
                    </BaseButton>
                </div>

                <div class="mt-5 grid gap-5 lg:grid-cols-2">
                    <div>
                        <BaseLabel :for-id="`item_label_${index}`">Label</BaseLabel>
                        <BaseInput
                            :id="`item_label_${index}`"
                            v-model="item.label"
                            :name="`items[${index}][label]`"
                            required
                            :disabled="locked"
                        />
                        <BaseFieldError :message="errorFor(index, 'label')" />
                    </div>

                    <div>
                        <BaseLabel :for-id="`item_instructions_${index}`">Instructions</BaseLabel>
                        <BaseTextarea
                            :id="`item_instructions_${index}`"
                            v-model="item.instructions"
                            :name="`items[${index}][instructions]`"
                            placeholder="Optional guidance for the inspector."
                            :disabled="locked"
                        />
                        <BaseFieldError :message="errorFor(index, 'instructions')" />
                    </div>

                    <div class="lg:col-span-2">
                        <BaseCheckbox
                            :id="`item_required_${index}`"
                            v-model="item.is_required"
                            :name="`items[${index}][is_required]`"
                            :disabled="locked"
                        >
                            Required checklist item
                        </BaseCheckbox>
                        <BaseFieldError :message="errorFor(index, 'is_required')" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
