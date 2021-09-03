<template>
    <app-layout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
					<SchemaForm :schema="mySchema" :modelValue="myForm" />		
                </div>
				<pre>{{myForm}} </pre>
            </div>
        </div>
    </app-layout>
</template>

<script>
	import { computed, ref } from "vue";
    import AppLayout from '@/Layouts/AppLayout'
	import { SchemaForm, useSchemaForm } from 'formvuelate'
	import JetInput from '@/Jetstream/Input'

    export default {
        components: {
            AppLayout,
			SchemaForm,
        },
		setup () {
			const myForm = ref({})
		    useSchemaForm(myForm)
			return {
				myForm
			};
    	},
		data() {
            return {
				mySchema:[
  {
    component: JetInput,
    label: "First Name",
    model: "firstName",
  },
  {
    component:JetInput,
    label: "Last Name",
    model: "lastName",
  }
],
                mySchema1: [
				  {
					component: "h3",
					children: "New Invoice"
				  },
				  {
					type: "select",
					label: "Select Issuer Branch",
					name: "issuer_id",
					placeholder: "Select Branch",
					options: {
					  b1: "Branch 1",
					  b2: "Branch 2",
					  b3: "Branch 3"
					},
					validation: "required"
				  },
				  {
					type: "select",
					label: "Select Customer",
					name: "receiver_id",
					placeholder: "Select Customer",
					options: {
					  c1: "Customer 1",
					  c2: "Customer 2",
					  c3: "Customer 3"
					},
					validation: "required"
				  },
				  {
					component: "div",
					class: "flex-wrapper",
					children: [
					  {
						type: "date",
						label: "Invoice Date",
						name: "dateIssued",
						validation: "required"
					  },
					  {
						type: "time",
						label: "Invoice Time",
						name: "timeIssued",
						validation: "required"
					  }
					]
				  },
				  {
					type: "select",
					label: "Select Activity",
					name: "taxpayerActivityCode",
					placeholder: "Select Customer",
					options: {
					  a1: "Acitvity 1",
					  a2: "Acitivty 2",
					  a3: "Acitivty 3"
					},
					validation: "required"
				  },
				  {
					type: "text",
					label: "Internal Identifier",
					name: "internalID",
					placeholder: "internal identifier",
					validation: "required"
				  },
				  {
					type: "text",
					label: "Purchase Order",
					name: "purchaseOrderReference",
					placeholder: "optional"
				  },
				  {
					type: "text",
					label: "Purchase Order Description",
					name: "purchaseOrderDescription",
					placeholder: "optional"
				  },
				  {
					type: "text",
					label: "Sales Order",
					name: "salesOrderReference",
					placeholder: "optional"
				  },
				  {
					type: "text",
					label: "Sales Order Description",
					name: "salesOrderDescription",
					placeholder: "optional"
				  },
				  {
					type: "text",
					label: "Preforma Invoice Number",
					name: "proformaInvoiceNumber",
					placeholder: "optional"
				  },
				  {
					type: "text",
					label: "Total Sales Amount",
					name: "totalSalesAmount",
					placeholder: "",
					validation: "required"
				  },
				  {
					type: "text",
					label: "Total Discount Amount",
					name: "totalDiscountAmount",
					placeholder: "",
					validation: "required"
				  },
				  {
					type: "text",
					label: "Net Amount",
					name: "netAmount",
					placeholder: "",
					validation: "required"
				  },
				  {
					type: "text",
					label: "Total Amount",
					name: "totalAmount",
					placeholder: "",
					validation: "required"
				  },
				  {
					type: "text",
					label: "Extra Discount Amount",
					name: "extraDiscountAmount",
					placeholder: "",
					validation: "required"
				  },
				  {
					type: "text",
					label: "Total Items Discount Amount",
					name: "totalItemsDiscountAmount",
					placeholder: "",
					validation: "required"
				  },
				  {
					type: "group",
					name: "invoiceLines",
					validation: "min:1,length",
					repeatable: true,
					add_label: "+ Add Item",
					value: [
					  {}
					],
					children: [
					  {
						type: "select",
						name: "item_id",
						label: "Item",
						placeholder: "Select one",
						options: {
						  I1: "Item 1",
						  I2: "Item 2",
						  I3: "Item 3",
						  I4: "Item 4"
						}
					  },
					  {
						type: "select",
						name: "unitType",
						label: "Type",
						placeholder: "Select one",
						options: {
						  KG: "Weight (KG)",
						  EA: "Count"
						}
					  },
					  {
						type: "text",
						name: "quantity",
						label: "Quantity",
						validation: "required"
					  },
					  {
						type: "text",
						name: "salesTotal",
						label: "Total Sales",
						validation: "required"
					  },
					  {
						type: "text",
						name: "total",
						label: "Total",
						validation: "required"
					  },
					  {
						type: "text",
						name: "valueDifference",
						label: "Value Difference",
						validation: "required"
					  },
					  {
						type: "text",
						name: "totalTaxableFees",
						label: "Total Taxable Fees",
						validation: "required"
					  },
					  {
						type: "text",
						name: "netTotal",
						label: "Net Total",
						validation: "required"
					  },
					  {
						type: "text",
						name: "itemsDiscount",
						label: "Items Discount",
						validation: "required"
					  }
					]
				  }
				]
			}
		},
		props: {
			items: Object
  		},
		methods: {
			editItem: function(item_id) {
			}
		}
    }
</script>
