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
        <nav-bar
            @open:dlg1="openDlg1"
            @open:dlg2="openDlg2"
            @open:dlg3="openDlg3"
            @open:dlg4="openDlg4"
            @open:dlg5="openDlg5"
            @open:dlg6="openDlg6"
            @open:dlg7="openDlg7"
            @open:dlg8="openDlg8"
        />

        <div v-if="$page.props.flash.message" class="alert">
            {{ $page.props.flash.message }}
        </div>

        <div class="min-h-screen bg-gray-100">
            <main>
                <div class="pt-6">
                    <flash-message
                        type="success"
                        v-if="$page.props.flash.success"
                        :message="$page.props.flash.success"
                    />

                    <flash-message
                        type="error"
                        v-if="$page.props.flash.error"
                        :message="$page.props.flash.error"
                    />
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
import UploadItemsDialog from "@/Pages/Items/Upload";
import NavBar from "@/shared/NavBar";
import AppFooter from "@/shared/AppFooter";
import FlashMessage from "@/shared/FlashMessage";

export default {
    components: {
        UploadItemsDialog,
        UploadInvoicesDialog,
        NewCustomerDialog,
        NewItemDialog,
        NewUserDialog,
        NewBranchDialog,
        LoadItemsDialog,
        LoadInvoicesDialog,
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
    },
};
</script>
