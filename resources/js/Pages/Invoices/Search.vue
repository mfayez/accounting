<template>
  <app-layout>
    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="card-title flex flex-col lg:flex-row justify-between p-3">
          <h4 class="capitalize">
            {{ __("Search Invoices") }}
          </h4>
        </div>
        <div class="bg-white shadow-xl sm:rounded-lg px-4 pb-4 pt-4">
          <div class="grid lg:grid-cols-2 gap-4 sm:grid-cols-1 h-1/2 overflow">
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
            <jet-button @click="onSearch()">
              {{ __("Search") }}
            </jet-button>

            <jet-secondary-button class="ms-2" @click="onApprove()">
              {{ __("Approve Selected") }}
            </jet-secondary-button>

            <jet-secondary-button class="ms-2" @click="onDelete()">
              {{ __("Delete Selected") }}
            </jet-secondary-button>

            <jet-secondary-button class="ms-2" @click="onApproveAll()">
              {{ __("Approve All") }}
            </jet-secondary-button>

            <jet-secondary-button class="ms-2" @click="onDeleteAll()">
              {{ __("Delete All") }}
            </jet-secondary-button>
            <jet-secondary-button class="ms-2" @click="onFixDate()">
              {{ __("Fix Date") }}
            </jet-secondary-button>
            <!--
            <jet-secondary-button class="ms-2" @click="onDelay(1)">
              {{ __("Delay Selected 1 Day") }}
            </jet-secondary-button>
            <jet-secondary-button class="ms-2" @click="onDelay(2)">
              {{ __("Delay Selected 2 Day") }}
            </jet-secondary-button>
            <jet-secondary-button class="ms-2" @click="onDelay(3)">
              {{ __("Delay Selected 3 Day") }}
            </jet-secondary-button>
            -->
          </div>
        </div>
      </div>
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="result my-5 overflow-x-auto w-full" v-if="data.length > 0">
          <table class="w-11/12 mx-auto max-w-4xl lg:max-w-full">
            <thead class="text-center bg-gray-300">
              <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">
                <input type="checkbox" @click="checkAll()" />
                {{ __("Select All") }}
              </th>
              <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">
                {{ __("Invoice Number") }}
              </th>
              <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">
                {{ __("Month") }}
              </th>
              <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">
                {{ __("Date") }}
              </th>
              <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">
                {{ __("Tax Total") }}
              </th>
              <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">
                {{ __("Client Name") }}
              </th>
              <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">
                {{ __("Total Amount") }}
              </th>
            </thead>
            <tbody class="text-center border border-[#eceeef]">
              <tr
                class="border border-[#eceeef]"
                v-for="row in data"
                :key="row.InvID"
              >
                <td class="p-2 border border-[#eceeef]">
                  <input
                    type="checkbox"
                    v-model="row.selected"
                    :value="row.InvID"
                  />
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

<script>
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
  props: {},
  data() {
    return {
      branches: [],
      customers: [],
      statuses: [],
      selected_status: null,
      data: [],
      form: {
        issuer: "",
        receiver: "",
        startDate: new Date().toISOString().slice(0, 10),
        endDate: new Date().toISOString().slice(0, 10),
        status: "",
      },
    };
  },
  methods: {
    onSearch: function () {
      this.form.status = this.selected_status.value;
      axios
        .post(route("invoices.searchData"), this.form)
        .then((response) => {
          this.data = response.data;
        })
        .catch((error) => {});
    },
    checkAll() {
      this.$nextTick(() => {
        this.data.forEach((row) => {
          row.selected = true;
        });
      });
    },
    onApprove() {
      var temp = this.data.filter((row) => row.selected);
      if (temp.length == 0) {
        swal(this.__("Please select at least one invoice!"), {
          icon: "error",
        });
        return;
      }
      swal({
        title: this.__("Are you sure?"),
        text: this.__("Once approved it will be sent to ETA"),
        icon: "warning",
        buttons: true,
        dangerMode: true,
      }).then((willApprove) => {
        if (willApprove) {
          axios
            .post(route("eta.invoices.approve"), {
              Id: this.data
                .filter((row) => row.selected)
                .map((row) => row.InvID),
            })
            .then((response) => {
              swal(this.__("Invoices has been approved!"), {
                icon: "success",
              }).then(() => {
                this.onSearch();
              });
            })
            .catch((error) => {
              swal(error.response.data, {
                icon: "error",
              });
            });
        }
      });
    },
    onDelay(p_days) {
      var temp = this.data.filter((row) => row.selected);
      if (temp.length == 0) {
        swal(this.__("Please select at least one invoice!"), {
          icon: "error",
        });
        return;
      }
      swal({
        title: this.__("Are you sure?"),
        text: this.__("Once clicked ok  the selected invoices will be delayed"),
        icon: "warning",
        buttons: true,
        dangerMode: true,
      }).then((willApprove) => {
        if (willApprove) {
          axios
            .post(route("eta.invoices.delay"), {
              Id: this.data
                .filter((row) => row.selected)
                .map((row) => row.InvID),
              days: p_days,
            })
            .then((response) => {
              swal(this.__("Invoices has been delayed!"), {
                icon: "success",
              }).then(() => {
                this.onSearch();
              });
            })
            .catch((error) => {
              swal(error.response.data, {
                icon: "error",
              });
            });
        }
      });
    },
    onDelete() {
      var temp = this.data.filter((row) => row.selected);
      if (temp.length == 0) {
        swal(this.__("Please select at least one invoice!"), {
          icon: "error",
        });
        return;
      }
      swal({
        title: this.__("Are you sure?"),
        text: this.__("Once clicked ok  the selected invoices will be deleted"),
        icon: "warning",
        buttons: true,
        dangerMode: true,
      }).then((willApprove) => {
        if (willApprove) {
          axios
            .post(route("eta.invoices.delete"), {
              Id: this.data
                .filter((row) => row.selected)
                .map((row) => row.InvID),
            })
            .then((response) => {
              swal(this.__("Invoices has been deleted!"), {
                icon: "success",
              }).then(() => {
                this.onSearch();
              });
            })
            .catch((error) => {
              swal(error.response.data, {
                icon: "error",
              });
            });
        }
      });
    },
    onApproveAll() {
      this.data.forEach((row) => {
        row.selected = true;
      });
      swal({
        title: this.__("Are you sure?"),
        text: this.__("Once approved it will be sent to ETA"),
        icon: "warning",
        buttons: true,
        dangerMode: true,
      }).then((willApprove) => {
        if (willApprove) {
          axios
            .post(route("eta.invoices.approve"), {
              Id: this.data
                .filter((row) => row.selected)
                .map((row) => row.InvID),
            })
            .then((response) => {
              swal(this.__("Invoices has been approved!"), {
                icon: "success",
              }).then(() => {
                this.onSearch();
              });
            })
            .catch((error) => {
              swal(error.response.data, {
                icon: "error",
              });
            });
        }
      });
    },
    onFixDate() {
      var temp = this.data.filter((row) => row.selected);
      if (temp.length == 0) {
        swal(this.__("Please select at least one invoice!"), {
          icon: "error",
        });
        return;
      }
      swal({
        title: this.__("Are you sure?"),
        text: this.__("Once clicked ok  the selected invoices' date will be set to current day"),
        icon: "warning",
        buttons: true,
        dangerMode: true,
      }).then((willApprove) => {
        if (willApprove) {
          axios
            .post(route("eta.invoices.fixDate"), {
              Id: this.data
                .filter((row) => row.selected)
                .map((row) => row.InvID),
            })
            .then((response) => {
              swal(this.__("Invoices' date has been fixed!"), {
                icon: "success",
              }).then(() => {
                this.onSearch();
              });
            })
            .catch((error) => {
              swal(error.response.data, {
                icon: "error",
              });
            });
        }
      });
    },
    onDeleteAll() {
      this.data.forEach((row) => {
        row.selected = true;
      });
      swal({
        title: this.__("Are you sure?"),
        text: this.__("Once clicked ok all invoices will be deleted"),
        icon: "warning",
        buttons: true,
        dangerMode: true,
      }).then((willApprove) => {
        if (willApprove) {
          axios
            .post(route("eta.invoices.delete"), {
              Id: this.data.filter((row) => row.selected).map((row) => row.InvID),
            })
            .then((response) => {
              swal(this.__("Invoices has been deleted!"), {
                icon: "success",
              }).then(() => {
                this.onSearch();
              });
            })
            .catch((error) => {
              swal(error.response.data, {
                icon: "error",
              });
            });
        }
      });
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

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>


<!-- onDeleteAll: function () {
  //let deletedInv = this.data.map((row) => row.InvID);
  axios
    .post(route("invoices.serach.delete"), [{ id: 1, name: "n" }])
    .then((response) => {
      // console.log(response);
      // this.data = [];
    })
    .catch((err) => {});
},
onDeleteSelected: function () {}, -->