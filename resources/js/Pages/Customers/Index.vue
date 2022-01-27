<template>
    <app-layout>
        <edit-customer ref="dlg2" :customer="customer" />
        <confirm ref="dlg1" @confirmed="remove()">
            {{ __("Are you sure you want to delete this customer?") }}
        </confirm>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="wrapper Gbg-white overflow-hidden shadow-xl sm:rounded-lg p-4"
                >
                    <Table
                        :filters="queryBuilderProps.filters"
                        :search="queryBuilderProps.search"
                        :columns="queryBuilderProps.columns"
                        :on-update="setQueryBuilder"
                        :meta="customers"
                    >
                        <template #head>
                            <tr>
                                <th
                                    v-show="showColumn('Id')"
                                    @click.prevent="sortBy('Id')"
                                >
                                    {{ __("ID") }}
                                </th>
                                <th
                                    v-show="showColumn('name')"
                                    @click.prevent="sortBy('name')"
                                >
                                    {{ __("Name") }}
                                </th>
                                <th
                                    v-show="showColumn('receiver_id')"
                                    @click.prevent="sortBy('receiver_id')"
                                >
                                    {{ __("Registration Number") }}
                                </th>

                                <th
                                    v-show="showColumn('type')"
                                    @click.prevent="sortBy('type')"
                                >
                                    {{ __("Type(B|I)") }}
                                </th>
                                <th @click.prevent="">{{ __("Actions") }}</th>
                            </tr>
                        </template>

                        <template #body>
                            <tr
                                v-for="customer in customers.data"
                                :key="customer.Id"
                            >
                                <td v-show="showColumn('Id')">
                                    {{ customer.Id }}
                                </td>
                                <td v-show="showColumn('name')">
                                    {{ customer.name }}
                                </td>
                                <td v-show="showColumn('receiver_id')">
                                    {{ customer.receiver_id }}
                                </td>
                                <td v-show="showColumn('type')">
                                    {{
                                        customer.type == "B"
                                            ? __("Business")
                                            : __("Person")
                                    }}
                                </td>
                                <td>
                                    <secondary-button
                                        @click="editCustomer(customer)"
                                    >
                                        <i class="fa fa-edit"></i>
                                        {{ __("Edit") }}
                                    </secondary-button>
                                    <jet-button
                                        class="ms-2"
                                        @click="removeCustomer(customer)"
                                    >
                                        <i class="fa fa-trash"></i>
                                        {{ __("Delete") }}
                                    </jet-button>
                                </td>
                            </tr>
                        </template>
                    </Table>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Confirm from "@/UI/Confirm";
import EditCustomer from "@/Pages/Customers/Edit";
import {
    InteractsWithQueryBuilder,
    Tailwind2,
} from "@protonemedia/inertiajs-tables-laravel-query-builder";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";

export default {
    mixins: [InteractsWithQueryBuilder],
    components: {
        EditCustomer,
        Confirm,
        AppLayout,
        Table: Tailwind2.Table,
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
                    //this.$refs.password.focus()
                });
        },
    },
};
</script>
<style scoped>
:deep(table th) {
    text-align: start;
}
</style>
