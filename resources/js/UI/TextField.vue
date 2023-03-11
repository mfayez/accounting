<template>
    <div>
        <jet-label :for="itemId" :value="itemLabel" />
        <jet-input :inputId="itemId" :type="itemType" class="mt-1 block w-full h-9 disabled:bg-gray-400"
            v-bind:model-value="modelValue" required autofocus @update:modelValue="$emit('update:modelValue', $event)"
            :disabled="!active" />
    </div>
</template>

<script>
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'

export default {
    components: {
        JetInput,
        JetLabel,
    },
    props: {
        itemLabel: String,
        itemId: String,
        itemType: String,
        //define modelValue to have string or number type
        modelValue: {
            type: [String, Number],
            default: ''
        },
        active: {
            Type: Boolean,
            default: true
        }
    },
    emits: ['update:modelValue'],

    data() {
        return {
        }
    },

    methods: {
    },
}
</script>