<template>
    <app-layout>
        <edit-customer ref="dlg2" :customer="customer" />
        <confirm ref="dlg1" @confirmed="remove()">
            {{ __("Are you sure you want to delete this customer?") }}
        </confirm>
        <div class="py-4">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="wrapper Gbg-white overflow-hidden shadow-xl sm:rounded-lg p-4"
                >
                    <Table :resource="customers" >
                        <template #cell(type)="{ item: customer }">
                            {{
                                customer.type == "B"
                                    ? __("Business")
                                    : customer.type == "P"
                                    ? __("Person")
                                    : __("Foreign Customer")
                            }}
                        </template>
                        <template #cell(actions)="{ item: customer }">
                            <secondary-button @click="editCustomer(customer)">
                                <i class="fa fa-edit"></i> {{ __("Edit") }}
                            </secondary-button>
                            <jet-button class="ms-2" @click="removeCustomer(customer)">
                                <i class="fa fa-trash"></i> {{ __("Delete") }}
                            </jet-button>
                        </template>
                    </Table>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import Confirm from "@/UI/Confirm.vue";
import EditCustomer from "@/Pages/Customers/Edit.vue";
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import axios from 'axios';

export default {
    components: {
        EditCustomer,
        Confirm,
        AppLayout,
        Table,
        SecondaryButton,
        JetButton,
    },
    props: {
        customers: Object,
    },
    data() {
        return {
            customer: Object,
        };
    },
    methods: {
        editCustomer(cust) {
            this.customer = cust;
            this.$nextTick(() => this.$refs.dlg2.ShowDialog());
            //this.$refs.dlg2.ShowDialog();
        },
        removeCustomer(cust) {
            this.customer = cust;
            this.$refs.dlg1.ShowModal();
        },
        remove() {
            axios
                .delete(
                    route("customers.destroy", { customer: this.customer.Id })
                )
                .then((response) => {
                    this.$store.dispatch("setSuccessFlashMessage", true);
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                })
                .catch((error) => {
                });
        },
        showColumn(columnKey) {
          if (!this.$inertia.page.props.queryBuilderProps.default.columns) {
            return false;
          }
          const column = this.$inertia.page.props.queryBuilderProps.default.columns.find(
            item => item.key === columnKey
          );
          return column ? !column.hidden : false;
        },
    },
};
</script>
<style scoped>
:deep(table th) {
    text-align: start;
    margin-block-start: 4px;
}
</style>
