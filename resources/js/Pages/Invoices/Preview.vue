<template>
    <jet-dialog-modal :show="showDlg" max-width="5xl" @close="showDlg = false">
        <template #title>
            {{ __("Invoice Preview") }}
        </template>

        <template #content>
            <div class="grid grid-cols-10 gap-0 mt-2">
                <div class="col-span-5">{{__('Branch')}}: {{ item.issuer.name }}</div>
                <div class="col-span-5">{{__('Customer')}}: {{ item.receiver.name }}</div>
                <div class="col-span-5">{{__('Date')}}: {{ getDate(item.dateTimeIssued) }}</div>
            </div>
            <div class="grid grid-cols-10 gap-0 mt-2">
                <div class="bg-gray-400 col-span-3">{{__('Item')}}</div>
                <div class="bg-gray-400 col-span-2">{{__('Code')}}</div>
                <div class="bg-gray-400 col-span-1">{{__('Quantity')}}</div>
                <div class="bg-gray-400 col-span-1">{{__('Unit Price')}}</div>
                <div class="bg-gray-400 col-span-1">{{__('Sales')}}</div>
                <div class="bg-gray-400 col-span-1">{{__('Tax')}}</div>
                <div class="bg-gray-400 col-span-1">{{__('Total')}}</div>
                <template
                    v-for="(invline, index) in item.invoice_lines"
                    :key="index"
                >
                    <div
                        class="col-span-3"
                        :class="{ 'bg-gray-200': index % 2 == 1 }"
                    >
                        {{ invline.description }}
                    </div>
                    <div
                        class="col-span-2"
                        :class="{ 'bg-gray-200': index % 2 == 1 }"
                    >
                        {{ invline.itemCode }}
                    </div>
                    <div
                        class="col-span-1"
                        :class="{ 'bg-gray-200': index % 2 == 1 }"
                    >
                        {{ invline.quantity.toFixed(2) }}
                    </div>
                    <div
                        class="col-span-1"
                        :class="{ 'bg-gray-200': index % 2 == 1 }"
                    >
                        {{ (Math.round(100*invline.unit_value.amountEGP) / 100).toFixed(2)  }}
                    </div>
                    <div
                        class="col-span-1"
                        :class="{ 'bg-gray-200': index % 2 == 1 }"
                    >
                        {{ (Math.round(100*invline.salesTotal) / 100).toFixed(2)  }}
                    </div>
                    <div
                        class="col-span-1"
                        :class="{ 'bg-gray-200': index % 2 == 1 }"
                    >
                        {{ getTaxlines(invline).toFixed(2)  }}
                    </div>
                    <div
                        class="col-span-1"
                        :class="{ 'bg-gray-200': index % 2 == 1 }"
                    >
                        {{ (Math.round(100*invline.total) / 100).toFixed(2) }}
                    </div>
                </template>
                <div class="bg-gray-400 col-span-5">{{__('Summary')}}</div>
                <div class="bg-gray-400 col-span-1">****</div>
                <div class="bg-gray-400 col-span-1">****</div>
                <div class="bg-gray-400 col-span-1">
                    {{ (Math.round(100*item.totalSalesAmount) / 100).toFixed(2)  }}
                </div>
                <div class="bg-gray-400 col-span-1">{{ getTotalTax().toFixed(2)  }}</div>
                <div class="bg-gray-400 col-span-1">{{ (Math.round(100*item.totalAmount) / 100).toFixed(2)  }}</div>
            </div>
        </template>
        <template #footer>
            <div class="flex items-center justify-between mt-4">
                <jet-secondary-button @click="CancelDlg()">
                    {{__('Close')}}
                </jet-secondary-button>
                <div>
                    <jet-button class="ms-2" @click="ApproveItem()">
                        {{ __("Approve") }}
                    </jet-button>
                    <!--<jet-button class="ms-2" @click="CopyItem()">
                        {{ __("Copy") }}
                    </jet-button>-->
                </div>
            </div>
        </template>
    </jet-dialog-modal>
</template>

<script>
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetActionSection from "@/Jetstream/ActionSection";
import JetButton from "@/Jetstream/Button";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetFormSection from "@/Jetstream/FormSection";
import JetInput from "@/Jetstream/Input";
import JetCheckbox from "@/Jetstream/Checkbox";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import JetValidationErrors from "@/Jetstream/ValidationErrors";
import TextField from "@/UI/TextField";
import Multiselect from "@suadelabs/vue3-multiselect";
import axios from "axios";
import swal from "sweetalert";
export default {
    components: {
        TextField,
        Multiselect,
        JetActionMessage,
        JetActionSection,
        JetButton,
        JetConfirmationModal,
        JetDangerButton,
        JetDialogModal,
        JetFormSection,
        JetInput,
        JetCheckbox,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        JetSectionBorder,
        JetValidationErrors,
    },

    props: ["modelValue"],
    data() {
        return {
            item: this.modelValue,
            count: 1,
            units: [],
            items: [],
            taxTypes: [],
            taxSubtypes: [],
            taxSubtypes1: [],
            taxType: null,
            taxSubtype: null,
            showDlg: false,
        };
    },

    methods: {
        ShowDialog() {
            this.item = JSON.parse(JSON.stringify(this.modelValue));
            this.showDlg = true;
        },
        CancelDlg() {
            this.showDlg = false;
        },
		getDate(temp) {
			return (new Date(temp)).toLocaleDateString();
		},
        getTaxlines(invLine) {
            var total = 0;
            for (var j = 0; j < invLine.taxable_items.length; j++) {
                var taxitem = invLine.taxable_items[j];
                //var temp = taxitem.taxType + "(" + taxitem.subType + ")
                total = total + parseFloat(taxitem.amount);
            }
            return total;
        },
        getTotalTax() {
            var total = 0;
            for (var i = 0; i < this.item.invoice_lines.length; i++) {
                var invLine = this.item.invoice_lines[i];
                for (var j = 0; j < invLine.taxable_items.length; j++) {
                    var taxitem = invLine.taxable_items[j];
                    //var temp = taxitem.taxType + "(" + taxitem.subType + ")
                    total = total + parseFloat(taxitem.amount);
                }
            }
            return total;
        },
        ApproveItem() {
            axios
                .post(route("eta.invoices.approve"), {
                    Id: this.item.Id,
                })
                .then((response) => {
                    alert(response.data);
                })
                .catch((error) => {
                    alert(error.response.data);
                    //this.$refs.password.focus()
                });
        },
        CopyItem() {
            axios
                .post(route("invoices.copy"), {
                    Id: this.item.Id,
                })
                .then((res) => {
                    swal({
                        title: "Invoice Was Copied",
                        icon: "success",
                    });
                    setTimeout(() => {
                        window.location.reload();
                    }, 200);
                })
                .catch((error) => {
                    console.error(error);
                });
        },
    },
    created: function created() {},
};
</script>
