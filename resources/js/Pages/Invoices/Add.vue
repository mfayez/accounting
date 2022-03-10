<template>
    <app-layout>
        <div class="py-4">
            <dialog-invoice-line
                :invoice="invoice"
                v-model="currentItem"
                ref="dlg1"
                @update:model-value="onClose"
            />
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 pb-4 pt-0"
                >
                    <div
                        class="flex items-center ms-0 mb-4 border-b border-gray-200"
                    >
                        <jet-button
                            @click="tab_idx = 1"
                            :disabled="tab_idx == 1"
                            :isRounded="false"
                        >
                            {{ __("Invoice Summary") }}
                        </jet-button>
                        <jet-button
                            @click="tab_idx = 2"
                            :disabled="tab_idx == 2"
                            :isRounded="false"
                        >
                            {{ __("Optional Data (PO, SO)") }}
                        </jet-button>
                        <jet-button
                            @click="tab_idx = 3"
                            :disabled="tab_idx == 3"
                            :isRounded="false"
                        >
                            {{ __("Invoice Items") }}
                        </jet-button>
                    </div>
                    <!--First Tab-->
                    <div
                        v-show="tab_idx == 1"
                        class="grid lg:grid-cols-4 gap-4 sm:grid-cols-1 h-1/2 overflow"
                    >
                        <div>
                            <jet-label :value="__('Branch')" />
                            <multiselect
                                v-model="form.issuer"
                                label="name"
                                :options="branches"
                                placeholder="Select branch"
                            />
                        </div>
                        <div>
                            <jet-label :value="__('Customer')" />
                            <multiselect
                                v-model="form.receiver"
                                label="name"
                                :options="customers"
                                placeholder="Select customer"
                            />
                        </div>
                        <div class="lg:col-span-2">
                            <jet-label :value="__('Branch Activity')" />
                            <multiselect
                                v-model="form.taxpayerActivityCode"
                                label="Desc_ar"
                                :options="activities"
                                placeholder="Select activity"
                            />
                        </div>
                        <TextField
                            v-model="form.dateTimeIssued"
                            itemType="datetime-local"
                            :itemLabel="__('Invoice Date')"
                        />
                        <TextField
                            v-model="form.internalID"
                            itemType="text"
                            :itemLabel="__('Internal Invoice ID')"
                            :active="$page.props.auto_inv_num ? false : true"
                        />
                        <TextField
                            v-model="form.totalSalesAmount"
                            itemType="number"
                            :itemLabel="__('Total Sales Amount')"
                            :active="false"
                        />
                        <TextField
                            v-model="form.totalDiscountAmount"
                            itemType="number"
                            :itemLabel="__('Total Discount Amount')"
                            :active="false"
                        />
                        <TextField
                            v-model="form.netAmount"
                            itemType="number"
                            :itemLabel="__('Net Amount')"
                            :active="false"
                        />
                        <TextField
                            v-model="form.totalAmount"
                            itemType="number"
                            :itemLabel="__('Total Amount')"
                            :active="false"
                        />
                        <TextField
                            v-model="form.extraDiscountAmount"
                            itemType="number"
                            :itemLabel="__('Extra Discount Amount')"
                            :active="false"
                        />
                        <TextField
                            v-model="form.totalItemsDiscountAmount"
                            itemType="number"
                            :itemLabel="__('Total Items Discount Amount')"
                            :active="false"
                        />
                    </div>
                    <!--second tab-->
                    <div
                        v-show="tab_idx == 2"
                        class="grid lg:grid-cols-4 gap-4 sm:grid-cols-1 h-1/2 overflow"
                    >
                        <TextField
                            v-model="form.purchaseOrderReference"
                            itemType="text"
                            :itemLabel="__('Purchase Order')"
                        />
                        <TextField
                            v-model="form.purchaseOrderDescription"
                            itemType="text"
                            :itemLabel="__('Purchase Order Description')"
                        />
                        <TextField
                            v-model="form.salesOrderReference"
                            itemType="text"
                            :itemLabel="__('Sales Order')"
                        />
                        <TextField
                            v-model="form.salesOrderDescription"
                            itemType="text"
                            :itemLabel="__('Sales Order Description')"
                        />
                        <TextField
                            v-model="form.purchaseOrderReference"
                            itemType="text"
                            :itemLabel="__('Purchase Order Reference')"
                        />
                        <TextField
                            v-model="form.proformaInvoiceNumber"
                            itemType="text"
                            :itemLabel="__('Proforma Invoice Number')"
                        />
                    </div>
                    <!--third tab-->
                    <div v-show="tab_idx == 3">
                        <div class="grid grid-cols-10 gap-0 mt-2">
                            <div class="bg-gray-200 col-span-3">
                                {{ __("Item") }}
                            </div>
                            <div class="bg-gray-200 col-span-1">
                                {{ __("Unit Price") }}
                            </div>
                            <div class="bg-gray-200 col-span-1">
                                {{ __("Quantity") }}
                            </div>
                            <div class="bg-gray-200 col-span-1">
                                {{ __("Sales Total") }}
                            </div>
                            <div class="bg-gray-200 col-span-1">
                                {{ __("Net Total") }}
                            </div>
                            <div class="bg-gray-200 col-span-1">
                                {{ __("Tax Items") }}
                            </div>
                            <div class="bg-gray-200 col-span-1"></div>
                            <div class="bg-gray-200 col-span-1"></div>
                            <template v-for="(item, idx1) in form.invoiceLines">
                                <jet-label class="mt-2 col-span-3">{{
                                    item.item.codeNamePrimaryLang
                                }}</jet-label>
                                <jet-label class="mt-2 col-span-1">{{
                                    item.unitValue.amountEGP
                                }}</jet-label>
                                <jet-label class="mt-2 col-span-1">{{
                                    item.quantity
                                }}</jet-label>
                                <jet-label class="mt-2 col-span-1">{{
                                    item.salesTotal
                                }}</jet-label>
                                <jet-label class="mt-2 col-span-1">{{
                                    item.netTotal
                                }}</jet-label>
                                <div class="grid grid-cols-2 gap-1">
                                    <template
                                        v-for="(taxitem, idx1) in item.taxItems"
                                        :key="taxitem.key"
                                    >
                                        <jet-label class="mt-2 col-span-2">{{
                                            getTaxStr(taxitem)
                                        }}</jet-label>
                                        <jet-label class="mt-2 col-span-2"
                                            >{{ taxitem.value }}({{
                                                taxitem.percentage
                                            }}%)</jet-label
                                        >
                                    </template>
                                </div>
                                <jet-secondary-button
                                    @click="EditItem(item, idx1)"
                                    class="h-12 mt-2 ms-2"
                                >
                                    {{ __("Edit") }}
                                </jet-secondary-button>
                                <jet-danger-button
                                    @click="DeleteItem(idx1)"
                                    class="h-12 mt-2 ms-2"
                                >
                                    {{ __("Delete") }}
                                </jet-danger-button>
                            </template>
                            <jet-label
                                class="col-span-8"
                                v-if="!form.invoiceLines.length"
                            >
                                {{ __("Please Add at least one item") }}
                            </jet-label>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <jet-button class="ms-2" @click="AddItem()">
                                {{ __("Add New Item") }}
                            </jet-button>
                        </div>
                        <div
                            v-for="(item, idx1) in form.invoiceLines"
                            class="border border-black"
                        >
                            <!--<pre>{{item}}</pre>-->
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-20">
                        <jet-secondary-button @click="onCancel()">
                            {{ __("Cancel") }}
                        </jet-secondary-button>

                        <jet-button class="ms-2" @click="onSave()">
                            {{ __("Save") }}
                        </jet-button>
                    </div>
                </div>
                <!--<pre> {{form}} </pre>-->
            </div>
        </div>
    </app-layout>
</template>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>

<script>
import { computed, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import JetLabel from "@/Jetstream/Label";
import JetButton from "@/Jetstream/Button";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetDangerButton from "@/Jetstream/DangerButton";
import TextField from "@/UI/TextField";
import Multiselect from "@suadelabs/vue3-multiselect";
import DialogInvoiceLine from "@/Pages/Invoices/EditLine";

export default {
    components: {
        AppLayout,
        JetLabel,
        JetButton,
        JetSecondaryButton,
        JetDangerButton,
        DialogInvoiceLine,
        TextField,
        Multiselect,
    },
    props: {
        invoice: {
            Type: Object,
            default: null,
        },
        items: {
            Type: Object,
            default: null,
        },
    },
    data() {
        return {
            addingNewLine: true,
            currentItemIdx: 0,
            tab_idx: 1,
            currentItem: { quantity: 1009 },
            branches: [],
            customers: [],
            activities: [],
            errors: [],
            form: this.$inertia.form({
                issuer: "",
                receiver: "",
                name: "",
                dateTimeIssued: new Date().toISOString().slice(0, 16),
                taxpayerActivityCode: "",
                internalID: this.$page.props.auto_inv_num ? "automatic" : "0",
                purchaseOrderReference: "",
                purchaseOrderDescription: "",
                salesOrderReference: "",
                salesOrderDescription: "",
                purchaseOrderReference: "",
                proformaInvoiceNumber: "",
                totalSalesAmount: 0,
                totalDiscountAmount: 0,
                netAmount: 0,
                totalAmount: 0,
                extraDiscountAmount: 0,
                totalItemsDiscountAmount: 0,
                invoiceLines: [],
                taxTotals: [],
            }),
        };
    },
    methods: {
        RecalculateTax: function () {
            this.form.totalSalesAmount = 0;
            this.form.totalDiscountAmount = 0;
            this.form.netAmount = 0;
            this.form.totalAmount = 0;
            this.form.extraDiscountAmount = 0;
            this.form.totalItemsDiscountAmount = 0;
            var taxTotals = {};
            this.form.taxTotals = [];
            for (var i = 0; i < this.form.invoiceLines.length; i++) {
                var item = this.form.invoiceLines[i];

                this.form.totalSalesAmount += parseFloat(item.salesTotal);
                this.form.totalDiscountAmount += parseFloat(item.itemsDiscount);
                this.form.netAmount += parseFloat(item.netTotal);
                this.form.totalAmount += parseFloat(item.total);
                this.form.extraDiscountAmount += 0;
                this.form.totalItemsDiscountAmount += parseFloat(
                    item.itemsDiscount
                );

                for (var j = 0; j < item.taxItems.length; j++) {
                    var taxitem = item.taxItems[j];
                    if (taxitem.taxType.Code in taxTotals)
                        taxTotals[taxitem.taxType.Code] =
                            taxTotals[taxitem.taxType.Code] +
                            parseFloat(taxitem.value);
                    else
                        taxTotals[taxitem.taxType.Code] = parseFloat(
                            taxitem.value
                        );
                }
            }
            for (let item of Object.keys(taxTotals))
                this.form.taxTotals.push({
                    taxType: item,
                    amount: taxTotals[item],
                });
        },
        AddItem: function () {
            this.addingNewLine = true;
            this.currentItem = {
                quantity: 1,
                totalTaxableFees: 0,
                itemsDiscount: 0,
                valueDifference: 0,
                unitValue: { amountEGP: 0 },
            };
            this.$nextTick(() => {
                this.$refs.dlg1.ShowDialog();
            });
            this.RecalculateTax();
        },
        DeleteItem: function (idx) {
            this.form.invoiceLines.splice(idx, 1);
            this.RecalculateTax();
        },
        EditItem: function (item, idx) {
            this.addingNewLine = false;
            this.currentItem = item;
            this.currentItemIdx = idx;
            this.$nextTick(() => {
                this.$refs.dlg1.ShowDialog();
            });
            this.RecalculateTax();
        },
        onClose: function () {
            this.currentItem.description =
                this.$page.props.locale == "ar"
                    ? this.currentItem.item.descriptionSecondaryLang
                    : this.currentItem.item.descriptionPrimaryLang;

            this.currentItem.itemType = this.currentItem.item.codeTypeName;
            this.currentItem.itemCode = this.currentItem.item.itemCode;
            this.currentItem.unitType = this.currentItem.unit.code;
            this.currentItem.internalCode = this.currentItem.item.Id.toString();
            var temp = this.currentItem.unitValue.amountEGP;
            this.currentItem.unitValue = {};
            this.currentItem.unitValue.currencySold = "EGP";
            this.currentItem.unitValue.amountEGP = temp;
            this.currentItem.taxableItems = this.currentItem.taxItems.map(
                function (taxitem) {
                    var obj = {};
                    obj.taxType = taxitem.taxType.Code;
                    obj.amount = taxitem.value;
                    obj.subType = taxitem.taxSubtype.Code;
                    obj.rate = taxitem.percentage;
                    return obj;
                }
            );
            //delete this.currentItem.item;
            if (this.addingNewLine)
                this.form.invoiceLines.push(this.currentItem);
            else this.form.invoiceLines[this.currentItemIdx] = this.currentItem;
            this.addingNewLine = false;
            this.RecalculateTax();
        },
        onCancel: function () {
            window.history.back();
        },
        onSave: function () {
            if (this.invoice) this.form.Id = this.invoice.Id;
            axios
                .post(route("eta.invoices.store"), this.form)
                .then((response) => {
                    this.processing = false;
                    this.$nextTick(() => this.$emit("dataUpdated"));
                    this.form.reset();
                    this.form.processing = false;
                    this.addingNew = false;
                    this.tab_idx = 1;
                })
                .catch((error) => {
                    this.form.processing = false;
                    this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors; //.password[0];
                    //this.$refs.password.focus()
                });
        },
        getTaxStr: function (taxitem) {
            if (taxitem.taxType.Code)
                return (
                    taxitem.taxType.Code + "(" + taxitem.taxSubtype.Code + ")"
                );
            return taxitem.taxType + "(" + taxitem.subType + ")";
        },
    },
    created: function created() {
        axios
            .get(route("json.branches"))
            .then((response) => {
                this.branches = response.data;
                if (this.invoice)
                    this.form.issuer = this.branches.find(
                        (option) => option.Id === this.invoice.issuer_id
                    );
            })
            .catch((error) => {});
        axios
            .get(route("json.customers"))
            .then((response) => {
                this.customers = response.data;
                if (this.invoice)
                    this.form.receiver = this.customers.find(
                        (option) => option.Id === this.invoice.receiver_id
                    );
            })
            .catch((error) => {});
        axios
            .get("/json/ActivityCodes.json")
            .then((response) => {
                this.activities = response.data;
                if (this.invoice)
                    this.form.taxpayerActivityCode = this.activities.find(
                        (option) =>
                            option.code === this.invoice.taxpayerActivityCode
                    );
                else this.form.taxpayerActivityCode = this.activities[0];
            })
            .catch((error) => {});
        this.$nextTick(() => {
            if (this.invoice) {
                this.form.receiver = this.customers.find(
                    (option) => option.Id === this.invoice.receiver_id
                );
                //todo mfayez fix this this.invoice.receiver;
                this.form.name = this.invoice.name;
                this.form.taxpayerActivityCode =
                    this.invoice.taxpayerActivityCode;
                this.form.internalID = this.invoice.internalID;
                this.form.totalSalesAmount = this.invoice.totalSalesAmount;
                this.form.totalDiscountAmount =
                    this.invoice.totalDiscountAmount;
                this.form.netAmount = this.invoice.netAmount;
                this.form.totalAmount = this.invoice.totalAmount;
                this.form.extraDiscountAmount =
                    this.invoice.extraDiscountAmount;
                this.form.totalItemsDiscountAmount =
                    this.invoice.totalItemsDiscountAmount;
                this.form.invoiceLines = this.invoice.invoicelines;
                this.form.dateTimeIssued = this.invoice.dateTimeIssued.slice(
                    0,
                    16
                );
                for (var i = 0; i < this.form.invoiceLines.length; i++) {
                    this.form.invoiceLines[i].unitValue =
                        this.form.invoiceLines[i].unit_value;
                    this.form.invoiceLines[i].taxItems =
                        this.form.invoiceLines[i].taxable_items;
                    for (
                        var j = 0;
                        j < this.form.invoiceLines[i].taxItems.length;
                        j++
                    ) {
                        this.form.invoiceLines[i].taxItems[j].value =
                            this.form.invoiceLines[i].taxItems[j].amount;
                        this.form.invoiceLines[i].taxItems[j].percentage =
                            this.form.invoiceLines[i].taxItems[j].rate;
                        this.form.invoiceLines[i].taxItems[j].taxType = {
                            Code: this.form.invoiceLines[i].taxItems[j].taxType,
                        };
                        this.form.invoiceLines[i].taxItems[j].taxSubtype = {
                            Code: this.form.invoiceLines[i].taxItems[j].subType,
                        };
                        this.form.invoiceLines[i].taxItems[j].taxType.label =
                            this.form.invoiceLines[i].taxItems[j].taxType.Code;
                        this.form.invoiceLines[i].taxItems[j].taxSubtype.label =
                            this.form.invoiceLines[i].taxItems[
                                j
                            ].taxSubtype.Code;
                    }
                    this.form.invoiceLines[i].taxableItems =
                        this.form.invoiceLines[i].taxItems.map(function (
                            taxitem
                        ) {
                            var obj = {};
                            obj.taxType = taxitem.taxType.Code;
                            obj.amount = taxitem.value;
                            obj.subType = taxitem.taxSubtype.Code;
                            obj.rate = taxitem.percentage;
                            return obj;
                        });
                }
                this.RecalculateTax();
            }
        });
    },
};
</script>
