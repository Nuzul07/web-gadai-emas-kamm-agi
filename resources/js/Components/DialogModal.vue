<script setup>
import Modal from "./Modal.vue";

const props = defineProps({
    show: {
        default: false,
    },
    maxWidth: {
        default: "",
    },
    closeable: {
        default: true,
    },
    footer: {
        default: true,
    },
    classHeader: {
        default: "",
    },
    classContent: {
        default: "",
    },
    classFooter: {
        default: "",
    },
    classBg: {
        default: "",
    },
});

const emit = defineEmits(["close"]);

function close() {
    emit("close");
}
</script>
<template>
    <Modal
        :show="show"
        :maxWidth="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div :class="classBg">
            <div
                class="flex flex-row justify-between p-6 lg:py-2 lg:pl-4 lg:pr-4 w-full"
                :class="classHeader"
            >
                <h2 class="text-xl font-semibold p-1">
                    <slot name="title"></slot>
                </h2>

                <div class="px-1">
                <button
                    type="button"
                    @click="close"
                    class="p-1.5 h-9 w-9 bg-red-600 hover:bg-red-700 text-white rounded-xl float-right"
                    data-modal-hide="authentication-modal"
                >
                    <svg
                        class="w-6 h-6"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 14 14"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                        />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            </div>
        </div>

        <div class="px-6 lg:px-8" :class="classContent">
            <slot name="content"></slot>
        </div>
        <div class="px-6 lg:px-8" :class="classFooter">
            <slot name="footer"></slot>
        </div>
    </Modal>
</template>
