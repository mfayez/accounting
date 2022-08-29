<template>
    <jet-dialog-modal :show="showDlg" max-width="3xl" @close="CancelDlg">
        <template #title>
            {{ __("Add item to the invoice") }}
        </template>

        <template #content>
            <div class="grid grid-cols-4 gap-4 border-b border-gray-20 pb-4">
                <div class="col-span-4">
                    <jet-label :value="__('Item')" />
                    <multiselect
                        v-model="item.item"
                        :options="items"
                        :label="this.$page.props.locale == 'ar'
                                ? 'codeNameSecondaryLang'
                                : 'codeNamePrimaryLang'"
                    	@update:model-value="updateDescription"
                        :placeholder="__('Select item')"
                    />
                </div>
                <div class="col-span-4">
                    <TextField
                        v-model="item.description"
                        itemType="text"
                        :itemLabel="__('Custom Description')"
                        :active="$page.props.custom_desc_enabled"
                    />
                </div>
                <div class="col-span-4">
                    <jet-label :value="__('Units')" />
                    <multiselect
                        v-model="item.unit"
                        :options="units"
                        label="desc_en"
                        :placeholder="__('Select measurement unit')"
                    />
                </div>
                <TextField
                    v-model="item.quantity"
                    itemType="number"
                    :itemLabel="__('Quantity')"
                    @update:model-value="updateValues()"
                />
                <TextField
                    v-model="item.unitValue.amountEGP"
                    itemType="number"
                    :itemLabel="__('Unit Price')"
                    @update:model-value="updateValues()"
                />
                <TextField
                    v-model="item.salesTotal"
                    itemType="number"
                    :itemLabel="__('Sales Total')"
                    :active="false"
                />
                <TextField
                    v-model="item.total"
                    itemType="number"
                    :itemLabel="__('Total')"
                    :active="false"
                />
                <TextField
                    v-model="item.valueDifference"
                    itemType="number"
                    :itemLabel="__('Value Difference')"
                />
                <TextField
                    v-model="item.totalTaxableFees"
                    itemType="number"
                    :itemLabel="__('Total Taxable Fees')"
                />
                <TextField
                    v-model="item.netTotal"
                    itemType="number"
                    :itemLabel="__('Net Total')"
                    :active="false"
                />
                <TextField
                    v-model="item.itemsDiscount"
                    itemType="number"
                    :itemLabel="__('Items Discount')"
                    @update:model-value="updateValues()"
                />
            </div>
            <div
                class="grid grid-cols-7 gap-2 mt-2 border-t border-b border-gray-20"
            >
                <multiselect
                    class="col-span-3"
                    v-model="taxType"
                    label="label"
                    :placeholder="__('Select Tax Type')"
                    :options="taxTypes"
                    @update:model-value="updateTaxSubtypes"
                />
                <multiselect
                    class="col-span-3"
                    v-model="taxSubtype"
                    :placeholder="__('Select Tax Subtype')"
                    :options="taxSubtypes1"
                    label="label"
                />
                <!--<pre> {{item}} </pre>-->
                <div class="flex items-center justify-center">
                    <jet-secondary-button @click="AddTaxItem()">
                        {{ __("Add Tax") }}
                    </jet-secondary-button>
                </div>
            </div>
            <div class="grid grid-cols-7 gap-0 mt-2">
                <div class="bg-gray-200 col-span-2">{{ __("Tax Code") }}</div>
                <div class="bg-gray-200 col-span-2">
                    {{ __("Tax Subcode") }}
                </div>
                <div class="bg-gray-200 col-span-1">{{ __("Tax Amount") }}</div>
                <div class="bg-gray-200 col-span-1">
                    {{ __("Tax Percentage") }}
                </div>
                <div class="bg-gray-200 col-span-1"></div>
                <template
                    v-for="(taxitem, idx1) in item.taxItems"
                    :key="taxitem.key"
                >
                    <jet-label class="mt-2 col-span-2">{{
                        taxitem.taxType.label
                    }}</jet-label>
                    <jet-label class="mt-2 col-span-2">{{
                        taxitem.taxSubtype.label
                    }}</jet-label>
                    <jet-input
                        :id="taxitem.key"
                        type="number"
                        class="mt-1 block w-full mt-2 col-span-1"
                        :isRounded="false"
                        @update:model-value="updatePercentage(taxitem, $event)"
                        v-model="taxitem.value"
                        required
                        autofocus
                    />
                    <jet-input
                        :id="taxitem.key"
                        type="number"
                        class="mt-1 block w-full mt-2 col-span-1"
                        :isRounded="false"
                        @update:model-value="updateValue(taxitem, $event)"
                        v-model="taxitem.percentage"
                        required
                        autofocus
                    />
                    <jet-danger-button
                        @click="deleteItem(idx1)"
                        class="mt-2 ms-2"
                    >
                        {{ __("Delete") }}
                    </jet-danger-button>
                </template>
                <jet-label
                    class="col-span-7"
                    v-if="!item.taxItems || item.taxItems.length == 0"
                >
                    {{ __("Please Add tax items if applicable") }}
                </jet-label>
            </div>
        </template>
        <template #footer>
            <div class="flex items-center justify-end mt-4">
                <jet-secondary-button @click="CancelDlg()">
                    {{ __("Cancel") }}
                </jet-secondary-button>

                <jet-button class="ms-2" @click="SaveItem()">
                    {{ __("Save") }}
                </jet-button>
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

    props: ["modelValue", "invoice"],
    emits: ["update:modelValue"],

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
            if (this.item)
                this.item.unit = this.units.find(
                    (option) => option.code === this.item.unitType
                );
            this.taxTypes.forEach((type) => {
                    type.$isDisabled = false;
            });
        },
        CancelDlg() {
            this.showDlg = false;
            this.updateTaxTypes();
        },
		updateDescription() {
			if (this.$page.props.locale == 'ar')
				this.item.description = this.item.item.codeNameSecondaryLang;
			else
				this.item.description = this.item.item.codeNamePrimaryLang;
		},
        AddTaxItem() {
            if (!this.item.taxItems || this.item.taxItems.length == 0) {
                this.item.taxItems = [];
            }
            this.item.taxItems.push({
                taxType: this.taxType,
                taxSubtype: this.taxSubtype,
                value: 0,
                percentage: 0,
                key: this.count,
                temp: true,
            });
            // set selected taxtype to disabled

            this.taxTypes.filter(
                (type) => type.Code == this.taxType.Code
            )[0].$isDisabled = true;

            this.taxType = null;
            this.taxSubtype = null;
            this.count = this.count + 1;
        },
        updateTaxSubtypes() {
            this.taxSubtypes1 = this.taxSubtypes.filter((obj) => {
                if (obj.TaxtypeReference == this.taxType.Code) return obj;
            });
            this.subType = {};
        },
        calculateTax() {
            this.item.total = this.parse(this.item.netTotal);
            if (this.item.taxItems) {
                for (var j = 0; j < this.item.taxItems.length; j++) {
                    var taxitem = this.item.taxItems[j];
                    taxitem.value =
                        ((taxitem.percentage * this.item.netTotal) / 100.0);
                    if (taxitem.taxType.Code == "T4")
                        this.item.total -= taxitem.value;
                    else this.item.total += taxitem.value;
                }
            }
			this.item.total = this.item.total.toFixed(5);
        },
        parse(val) {
            var temp = parseFloat(val);
            if (isNaN(temp)) temp = 0;
            return temp;
        },
        updateValues() {
            this.item.salesTotal =
                (this.parse(this.item.quantity) *
                this.parse(this.item.unitValue.amountEGP)).toFixed(5);
            this.item.netTotal =
                (this.item.salesTotal - this.parse(this.item.itemsDiscount)).toFixed(5);
            this.calculateTax();
        },
        updateValue(item, val) {
            item.value = ((this.item.netTotal * val) / 100.0).toFixed(5);
            this.$nextTick(() => {
                this.calculateTax();
            });
        },
        updatePercentage(item, val) {
            item.percentage = (val * 100.0) / this.item.netTotal;
            this.$nextTick(() => {
                this.calculateTax();
            });
        },
        SaveItem() {
            this.item.isDirty = true;
            this.showDlg = false;
            if (!this.item.taxItems || this.item.taxItems.length == 0)
                this.item.taxItems = [];
            this.item.taxItems.forEach((type) => {
                type.temp = false;
            });
            this.$emit("update:modelValue", this.item);
        },
        deleteItem(idx1) {
            // set selected taxtype to disabled
            this.taxTypes.filter(
                (type) => type.Code == this.item.taxItems[idx1].taxType.Code
            )[0].$isDisabled = false;

            this.item.taxItems.splice(idx1, 1);
        },
        updateTaxTypes() {
            this.taxTypes.forEach((type) => {
                type.$isDisabled = false;
            });

            if (this.invoice) {
                const savedtaxItems = this.invoice.invoicelines.filter(
                    (line) => line.Id == this.item.Id
                )[0];
				if (savedtaxItems == null) return;

                const selectedTaxes = savedtaxItems.taxItems.map(
                    (type) => type.taxType.Code
                );
                this.taxTypes.forEach((type) => {
                    if (selectedTaxes.includes(type.Code)) {
                        type.$isDisabled = true;
                    }
                });
            } else {
                this.item.taxItems = this.item.taxItems.filter(
                    (item) => item.temp === false
                );

                const selectedTaxes = this.item.taxItems.map(
                    (type) => type.taxType.Code
                );
                this.taxTypes.forEach((type) => {
                    if (selectedTaxes.includes(type.Code)) {
                        type.$isDisabled = true;
                    }
                });
            }
        },
    },
    created: function created() {
        axios
            .get(route("json.eta.items"))
            .then((response) => {
                this.items = response.data;
            })
            .catch((error) => {});
        axios
            .get("/json/UnitTypes.json")
            .then((response) => {
                this.units = response.data;
                if (this.item)
                    this.item.unit = this.units.find(
                        (option) => option.code === this.item.unitType
                    );
            })
            .catch((error) => {});
        axios
            .get("/json/TaxSubtypes.json")
            .then((response) => {
                this.taxSubtypes = response.data;
                this.taxSubtypes = this.taxSubtypes.map((obj) => {
                    obj.label = obj.Code.concat("(", obj.Desc_ar, ")");
                    return obj;
                });
            })
            .catch((error) => {});
        axios
            .get("/json/TaxTypes.json")
            .then((response) => {
                this.taxTypes = response.data;
                this.taxTypes = this.taxTypes.map((obj) => {
                    obj.label = obj.Code.concat("(", obj.Desc_ar, ")");
                    return obj;
                });
            })
            .catch((error) => {});
    },
    watch: {
        showDlg(newValue, oldValue) {
            if (this.invoice) {
                this.updateTaxTypes();
            }
        },
    },
};
</script>
