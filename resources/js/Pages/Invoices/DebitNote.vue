<template>
    <jet-dialog-modal :show="showDlg" max-width="5xl" @close="showDlg = false">
        <template #title>
            {{ __("Invoice Debit Note") }}
        </template>

        <template #content>
            <div class="grid grid-cols-10 gap-0 mt-2">
                <div class="col-span-5">{{__('Branch')}}: {{ item.issuer.name }}</div>
                <div class="col-span-5">{{__('Customer')}}: {{ item.receiver.name }}</div>
                <div class="col-span-5">
                    <TextField
                        v-model="item.dateTimeIssued"
                        itemType="datetime-local"
                        :itemLabel="__('Invoice Date')"
                    />
                </div>
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
                        <TextField
                            v-model="invline.quantity"
                            itemType="number"
                            @update:model-value="updateValues(invline)"
                        />
                    </div>
                    <div
                        class="col-span-1"
                        :class="{ 'bg-gray-200': index % 2 == 1 }"
                    >
                        <TextField
                            v-model="invline.unit_value.amountEGP"
                            itemType="number"
                            @update:model-value="updateValues(invline)"
                        />
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
                <div v-show="item.status=='Valid'">
                    <jet-button class="ms-2" @click="SubmitDebitNote()">
                        {{ __("Approve") }}
                    </jet-button>
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
            this.item.dateTimeIssued = new Date().toISOString().slice(0, 16);
            this.showDlg = true;
        },
        CancelDlg() {
            this.showDlg = false;
        },
		getDate(temp) {
			return (new Date(temp)).toLocaleDateString();
		},
        updateValues(invline) {
            invline.salesTotal =
                this.parse(invline.quantity) *
                this.parse(invline.unit_value.amountEGP);
            invline.netTotal =
                invline.salesTotal - this.parse(invline.itemsDiscount);
            this.calculateTax(invline);
            this.RecalculateTax();
        },
        parse(val) {
            var temp = parseFloat(val);
            if (isNaN(temp)) temp = 0;
            return temp;
        },
        calculateTax(invline) {
            invline.total = invline.netTotal + 0;
            if (invline.taxable_items) {
                for (var j = 0; j < invline.taxable_items.length; j++) {
                    var taxitem = invline.taxable_items[j];
                    taxitem.amount =
                        (taxitem.rate * invline.netTotal) / 100.0;
                    if (taxitem.taxType == "T4")
                        invline.total -= taxitem.amount;
                    else invline.total += taxitem.amount;
                }
            }
        },
        RecalculateTax: function () {
            this.item.totalSalesAmount = 0;
            this.item.totalDiscountAmount = 0;
            this.item.netAmount = 0;
            this.item.totalAmount = 0;
            this.item.extraDiscountAmount = 0;
            this.item.totalItemsDiscountAmount = 0;
            var taxTotals = {};
            this.item.taxTotals = [];
            for (var i = 0; i < this.item.invoice_lines.length; i++) {
                var item = this.item.invoice_lines[i];

                this.item.totalSalesAmount += parseFloat(item.salesTotal);
                this.item.totalDiscountAmount += parseFloat(item.itemsDiscount);
                this.item.netAmount += parseFloat(item.netTotal);
                this.item.totalAmount += parseFloat(item.total);
                this.item.extraDiscountAmount += 0;
                this.item.totalItemsDiscountAmount += parseFloat(
                    item.itemsDiscount
                );

                for (var j = 0; j < item.taxable_items.length; j++) {
                    var taxitem = item.taxable_items[j];
                    if (taxitem.taxType in taxTotals)
                        taxTotals[taxitem.taxType] =
                            taxTotals[taxitem.taxType.Code] +
                            parseFloat(taxitem.amount);
                    else
                        taxTotals[taxitem.taxType] = parseFloat(
                            taxitem.amount
                        );
                }
            }
            for (let item of Object.keys(taxTotals))
                this.item.taxTotals.push({
                    taxType: item,
                    amount: taxTotals[item],
                });
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
        SubmitDebitNote() {
            axios
                .post(route("eta.invoices.debit.store"), this.item)
                .then((response) => {
                    swal({
                        title: "Invoice Debit Note Saved!",
                        icon: "success",
                    });
                })
                .catch((error) => {
                    swal({
                        title: "Invoice Debit Note Failed",
                        icon: "error",
                    });
                    //this.$refs.password.focus()
                });
        },
    },
    created: function created() {},
};
</script>
