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
        <add-pos ref="addPOS" />
        <load-receipts ref="loadReceipts" />
        <upload-receipts ref="uploadReceipts" />
        <archive-request-dialog2 ref="dlg12" />
        <nav-bar @open_dlg="openDlg" />
        <archive-request-dialog2 ref="dlg12" />
        <sb-load-invoices-dialog ref="dlg13" />
        <sb-items-map-dialog ref="dlg14" />
        <upload-customers-dialog ref="dlg15"/>
        <upload-accounting-chart ref="dlg16"/>

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
import UploadCustomersDialog from "@/Pages/Customers/Upload";
import SettingsDialog1 from "@/Pages/Reports/Settings";
import SettingsDialog2 from "@/Pages/Application/Settings";
import UploadItemsDialog from "@/Pages/Items/Upload";
import ArchiveRequestDialog from "@/Pages/Archives/Request";
import AddPos from "@/Pages/POS/Add.vue";
import LoadReceipts from "@/Pages/Receipts/Load";
import UploadReceipts from "@/Pages/Receipts/Upload";
import SbLoadInvoicesDialog from "@/Pages/SalesBuzz/Load"
import SbItemsMapDialog from "@/Pages/SalesBuzz/ItemsMap"
import UploadAccountingChart from "@/Pages/Accounting/Chart/Upload"
import ArchiveRequestDialog2 from "@/Pages/Archives/RequestEx";
import NavBar from "@/shared/NavBar";
import AppFooter from "@/shared/AppFooter";
import FlashMessage from "@/shared/FlashMessage";
import { mapGetters } from "vuex";
export default {
    components: {
        AddPos,
        LoadReceipts,
        UploadReceipts,
        ArchiveRequestDialog,
        ArchiveRequestDialog2,
        UploadItemsDialog,
        UploadInvoicesDialog,
        UploadCustomersDialog,
        NewCustomerDialog,
        NewItemDialog,
        NewUserDialog,
        NewBranchDialog,
        LoadItemsDialog,
        LoadInvoicesDialog,
		SettingsDialog1,
		SettingsDialog2,
        SbLoadInvoicesDialog,
        SbItemsMapDialog,
        NavBar,
        AppFooter,
        FlashMessage,
        UploadAccountingChart,
    },

    data() {
        return {
            showingNavigationDropdown: false,
        };
    },
    methods: {
        openDlg($dlg) {
            this.$refs[$dlg].ShowDialog();
        }
    },
    computed: {
        ...mapGetters(["successFlashMessage"]),
    },
};
</script>
