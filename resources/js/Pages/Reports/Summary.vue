<template>
    <app-layout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="card-title flex flex-col lg:flex-row justify-between p-3"
                >
                    <h4 class="capitalize">
                        {{ __("Summary Report") }}
                    </h4>
                </div>
                <div class="bg-white shadow-xl sm:rounded-lg px-4 pb-4 pt-4">
                    <div
                        class="grid lg:grid-cols-2 gap-4 sm:grid-cols-1 h-1/2 overflow"
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
                        <TextField
                            v-model="form.startDate"
                            itemType="date"
                            :itemLabel="__('Start Date')"
                        />
                        <TextField
                            v-model="form.endDate"
                            itemType="date"
                            :itemLabel="__('End Date')"
                        />
                        <div>
                            <jet-label :value="__('Status')" />
                            <multiselect
                                v-model="selected_status"
                                label="name"
                                :options="statuses"
                                placeholder="Select status"
                            />
                        </div>
                    </div>
                    <div class="flex items-center justify-start mt-4">
                        <jet-button @click="onShow()">
                            {{ __("Show") }}
                        </jet-button>

                        <jet-secondary-button class="ms-2" @click="onDownload()">
                            {{ __("Download") }}
                        </jet-secondary-button>

                        <jet-secondary-button class="ms-2" @click="downloadSummary">
                            {{ __("Download Summary") }}
                        </jet-secondary-button>

                        <jet-secondary-button class="ms-2" @click="onDownloadV2">
                            {{ __("Download Summary V2") }}
                        </jet-secondary-button>

                        <jet-secondary-button class="ms-2" @click="onDownloadV3">
                            {{ __("Download Summary Compact") }}
                        </jet-secondary-button>

                        <jet-secondary-button class="ms-2" @click="onPrint">
                            {{ __("Print") }}
                        </jet-secondary-button>

                        <jet-secondary-button class="ms-2" @click="onArchive">
                            {{ __("Archive") }}
                        </jet-secondary-button>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div
                    class="result my-5 overflow-x-auto w-full"
                    v-if="data.length > 0"
                >
                    <table class="w-11/12 mx-auto max-w-4xl lg:max-w-full">
                        <thead class="text-center bg-gray-300">
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                <input type="checkbox" v-model="allChecked" v-on:change="checkAll()" />
                                {{ __("Select All") }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __("Invoice Number") }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __("Month") }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __("Date") }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __("Tax Total") }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __("Client Name") }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __("Total Amount") }}
                            </th>
                        </thead>
                        <tbody class="text-center border border-[#eceeef]">
                            <tr
                                class="border border-[#eceeef]"
                                v-for="row in data"
                                :key="row"
                            >
                                <td class="p-2 border border-[#eceeef]">
                                    <input type="checkbox" v-model="row.print" v-on:change="updateCheckBoxes()"/>
                                </td>

                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.Id }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.Month }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.Date }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.TaxTotal }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.Client }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.Total }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <p class="text-center text-red-600 my-5">
                        <i class="fa fa-exclamation-circle mr-1"></i>
                        {{ __("No Records Were Found") }}
                    </p>
                </div>
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
import axios from "axios";
import swal from "sweetalert";

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
            branches: [],
            customers: [],
            data: [],
            errors: [],
            statuses: [],
            selected_status: null,
            form: this.$inertia.form({
                issuer: "",
                receiver: "",
                startDate: new Date().toISOString().slice(0, 10),
                endDate: new Date().toISOString().slice(0, 10),
                status: "",
            }),
            allChecked: false,
        };
    },
    methods: {
        onShow: function () {
            this.form.status = this.selected_status.value;
            axios
                .post(route("reports.summary.details.data"), this.form)
                .then((response) => {
                    this.data = response.data;
                    this.data.forEach((row) => {
                        row.print = false;
                    });
                    this.allChecked = false;
                })
                .catch((error) => {});
        },
        onDownload: function () {
            this.form.status = this.selected_status.value;
            axios({
                url: route("reports.summary.details.download"),
                method: "POST",
                data: this.form,
                responseType: "blob",
            }).then((response) => {
                const url = window.URL.createObjectURL(
                    new Blob([response.data])
                );
                const link = document.createElement("a");
                link.href = url;
                link.setAttribute("download", "report.xlsx");
                document.body.appendChild(link);
                link.click();
            });
        },
        downloadSummary() {
            this.form.status = this.selected_status.value;
            if (this.form.issuer && this.form.receiver) {
                axios
                    .post(
                        route("reports.summary.summaryOnlyData.download"),
                        this.form,
                        {
                            responseType: "blob",
                        }
                    )
                    .then((res) => {
                        const url = window.URL.createObjectURL(
                            new Blob([res.data])
                        );
                        const link = document.createElement("a");
                        link.href = url;
                        link.setAttribute("download", "report.xlsx");
                        document.body.appendChild(link);
                        link.click();
                    })
                    .catch((err) => {
                        console.error(err);
                    });
            } else {
                swal({
                    title:
                        document.body.lang == "en"
                            ? "Please Fill The Required Fileds"
                            : "برجاء ملئ الحقول المطلوبة",
                    icon: "error",
                });
            }
        },
        onDownloadV2() {
            this.form.status = this.selected_status.value;
            axios({
                url: route("reports.summary.details.download.new"),
                method: "POST",
                data: this.form,
                responseType: "blob",
            }).then((response) => {
                const url = window.URL.createObjectURL(
                    new Blob([response.data])
                );
                const link = document.createElement("a");
                link.href = url;
                link.setAttribute("download", "report.xlsx");
                document.body.appendChild(link);
                link.click();
            });
        },
        onDownloadV3() {
            this.form.status = this.selected_status.value;
            axios({
                url: route("reports.summary.details.download.compact"),
                method: "POST",
                data: this.form,
                responseType: "blob",
            }).then((response) => {
                const url = window.URL.createObjectURL(
                    new Blob([response.data])
                );
                const link = document.createElement("a");
                link.href = url;
                link.setAttribute("download", "report.xlsx");
                document.body.appendChild(link);
                link.click();
            });
        },
        checkAll() {
            this.$nextTick(() => {
                this.data.forEach((row) => {
                    row.print = this.allChecked;
                });
            });
        },
        updateCheckBoxes() {
            //next tick
            this.$nextTick(() => {
                this.allChecked = this.data.every((row) => row.print);
            });
        },
        onPrint() {
            let selected = this.data.filter((row) => row.print);
            let ids = selected.map((row) => row.LID);
            if (selected.length > 0) {
                window.open(route("pdf.invoices.preview", ids.join(',')));
            } else {
                swal({
                    title:
                        document.body.lang == "en"
                            ? "Please Select At Least One Record"
                            : "برجاء اختيار فاتورة واحدة على الأقل",
                    icon: "error",
                });
            }
        },
        onArchive() {
            let selected = this.data.filter((row) => row.print);
            let ids = selected.map((row) => row.LID);
            if (selected.length > 0) {
                window.open(route("zip.invoices", ids.join(',')));
            } else {
                swal({
                    title:
                        document.body.lang == "en"
                            ? "Please Select At Least One Record"
                            : "برجاء اختيار فاتورة واحدة على الأقل",
                    icon: "error",
                });
            }
        },
    },
    created: function created() {
        axios
            .get(route("json.branches"))
            .then((response) => {
                var temp = [{ Id: -1, name: "All" }];
                this.branches = temp.concat(response.data);
            })
            .catch((error) => {
                console.log(error);
            });
        axios
            .get(route("json.customers"))
            .then((response) => {
                var temp = [{ Id: -1, name: "All" }];
                this.customers = temp.concat(response.data);
            })
            .catch((error) => {
                console.log(error);
            });
        axios
            .get(route("reports.invoices.statuses"))
            .then((response) => {
                this.statuses = response.data;
            })
            .catch((error) => {
                console.log(error);
            });
    },
};
</script>
