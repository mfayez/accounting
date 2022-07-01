<template>
    <div>
        <new-customer-dialog ref="dlg1" />
        <new-branch-dialog ref="dlg2" />
        <load-items-dialog ref="dlg3" />
        <new-item-dialog ref="dlg4" />
        <load-invoices-dialog ref="dlg5" />
        <upload-invoices-dialog ref="dlg6" />
        <upload-items-dialog ref="dlg7" />
        <new-user-dialog ref="dlg8" />
        <settings-dialog1 ref="dlg9" />
        <settings-dialog2 ref="dlg10" />
        <archive-request-dialog ref="dlg11" />
        <add-pos ref="dlg12" />
        <nav-bar
            @open:dlg1="openDlg1"
            @open:dlg2="openDlg2"
            @open:dlg3="openDlg3"
            @open:dlg4="openDlg4"
            @open:dlg5="openDlg5"
            @open:dlg6="openDlg6"
            @open:dlg7="openDlg7"
            @open:dlg8="openDlg8"
            @open:dlg9="openDlg9"
            @open:dlg10="openDlg10"
            @open:dlg11="openDlg11"
            @open:dlg12="openDlg12"
        />

        <div class="min-h-screen bg-gray-100 pb-5">
            <main>
                <div class="pt-6">
                    <flash-message type="success" v-if="successFlashMessage" />
                </div>
                <slot></slot>
            </main>
        </div>
        <app-footer />
    </div>
</template>

<script>
// modals
import NewCustomerDialog from "@/Pages/Customers/Edit";
import NewBranchDialog from "@/Pages/Branches/Edit";
import NewUserDialog from "@/Pages/Users/Edit";
import LoadItemsDialog from "@/Pages/Items/Load";
import LoadInvoicesDialog from "@/Pages/Invoices/Load";
import NewItemDialog from "@/Pages/Items/Edit";
import UploadInvoicesDialog from "@/Pages/Invoices/Upload";
import SettingsDialog1 from "@/Pages/Reports/Settings";
import SettingsDialog2 from "@/Pages/Application/Settings";
import UploadItemsDialog from "@/Pages/Items/Upload";
import ArchiveRequestDialog from "@/Pages/Archives/Request";
import AddPos from "@/Pages/POS/Add.vue";
import NavBar from "@/shared/NavBar";
import AppFooter from "@/shared/AppFooter";
import FlashMessage from "@/shared/FlashMessage";
import { mapGetters } from "vuex";
export default {
    components: {
        AddPos,
        ArchiveRequestDialog,
        UploadItemsDialog,
        UploadInvoicesDialog,
        NewCustomerDialog,
        NewItemDialog,
        NewUserDialog,
        NewBranchDialog,
        LoadItemsDialog,
        LoadInvoicesDialog,
		SettingsDialog1,
		SettingsDialog2,
        NavBar,
        AppFooter,
        FlashMessage,
    },

    data() {
        return {
            showingNavigationDropdown: false,
        };
    },
    methods: {
        openDlg1() {
            this.$refs.dlg1.ShowDialog();
        },
        openDlg2() {
            this.$refs.dlg2.ShowDialog();
        },
        openDlg3() {
            this.$refs.dlg3.ShowDialog();
        },
        openDlg4() {
            this.$refs.dlg4.ShowDialog();
        },
        openDlg5() {
            this.$refs.dlg5.ShowDialog();
        },
        openDlg6() {
            this.$refs.dlg6.ShowDialog();
        },
        openDlg7() {
            this.$refs.dlg7.ShowDialog();
        },
        openDlg8() {
            this.$refs.dlg8.ShowDialog();
        },
        openDlg9() {
            this.$refs.dlg9.ShowDialog();
        },
        openDlg10() {
            this.$refs.dlg10.ShowDialog();
        },
        openDlg11() {
            this.$refs.dlg11.ShowDialog();
        },
        openDlg12() {
            this.$refs.dlg12.ShowDialog();
        },
    },
    computed: {
        ...mapGetters(["successFlashMessage"]),
    },
};
</script>
