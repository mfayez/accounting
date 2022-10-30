<template>
    <nav class="bg-white shadow dark:bg-gray-800">
        <div class="container px-6 py-4 mx-auto">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex items-center justify-between">
                    <div class="text-xl font-semibold text-gray-700">
                        <Link :href="route('dashboard')" class="flex">
                            <jet-application-mark class="w-10" />
                            <span class="self-center ms-3">Invoice Master</span>
                        </Link>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex md:hidden">
                        <button
                            @click="isOpen = !isOpen"
                            type="button"
                            class="text-gray-500 dark:text-gray-200 hover:text-gray-600 dark:hover:text-gray-400 focus:outline-none focus:text-gray-600 dark:focus:text-gray-400"
                            aria-label="toggle menu"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="w-6 h-6 fill-current"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M4 5h16a1 1 0 0 1 0 2H4a1 1 0 1 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2z"
                                ></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div
                    :class="!isOpen ? 'hidden' : ''"
                    class="flex-1 md:flex md:items-center md:justify-between"
                >
                    <div
                        class="flex flex-col -mx-4 md:flex-row md:items-center md:mx-5 mt-3 lg:mt-0"
                    >
                        <Link
                            :href="route('dashboard')"
                            :class="{ 'text-[#4099de]': $page.url === '/' }"
                            class="grid justify-items-center ms-3 mb-3 lg:mb-0"
                            ><i class="fas fa-chart-pie"></i>{{__("Dashboard")}}</Link
                        >
                        <dropdown
                            :align="alignDropDown()"
                            class="ms-3 mb-3 lg:mb-0"
                        >
                            <template #trigger>
                                <span
                                    class="grid justify-items-center cursor-pointer hover:text-[#4099de]"
                                    :class="{
                                        'text-[#4099de]':
                                            $page.url.startsWith('/customers'),
                                    }"
                                >
                                    <i class="fa fa-user-tie"></i>
                                    {{ __("Customers") }}
                                </span>
                            </template>
                            <template #content>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg1')"
                                    :href="route('customers.create')"
                                >
                                    {{ __("Add New Customer") }}
                                </dropdown-link>

                                <dropdown-link :href="route('customers.index')">
                                    {{ __("Show Customer") }}
                                </dropdown-link>

                                <dropdown-link as="a" :href="route('excel.customers')">
                                    {{ __("Download") }}
                                </dropdown-link>
                            </template>
                        </dropdown>
                        <dropdown
                            :align="alignDropDown()"
                            width="48"
                            class="ms-3 mb-3 lg:mb-0"
                        >
                            <template #trigger>
                                <span
                                    class="grid justify-items-center cursor-pointer hover:text-[#4099de]"
                                    :class="{
                                        'text-[#4099de]':
                                            $page.url.startsWith('/branches'),
                                    }"
                                >
                                    <i class="fa fa-code-branch"></i>
                                    {{ __("Branches") }}
                                </span>
                            </template>

                            <template #content>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg2')"
                                    href="#"
                                >
                                    {{ __("Add New Branch") }}
                                </dropdown-link>

                                <dropdown-link :href="route('branches.index')">
                                    {{ __("Show Branches") }}
                                </dropdown-link>
                            </template>
                        </dropdown>
                        <dropdown
                            :align="alignDropDown()"
                            width="48"
                            class="ms-3 mb-3 lg:mb-0"
                            v-show="$page.props.e_invoice_enabled"
                        >
                            <template #trigger>
                                <span
                                    class="grid justify-items-center cursor-pointer hover:text-[#4099de]"
                                    :class="{
                                        'text-[#4099de]': invoiceConditions,
                                    }"
                                >
                                    <i class="fa fa-file"></i>
                                    {{ __("Invoices") }}
                                </span>
                            </template>

                            <template #content>
                                <dropdown-link :href="route('invoices.create')">
                                    {{ __("Add New Invoice") }}
                                </dropdown-link>
                                <dropdown-link
                                    :href="route('eta.invoices.sent.index')"
                                >
                                    {{ __("Show My Invoices") }}
                                </dropdown-link>

                                <dropdown-link
                                    :href="route('eta.invoices.received.index')"
                                >
                                    {{ __("Show Paid Invoices") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg6')"
                                    href="#"
                                >
                                    {{ __("Upload Invoices") }}
                                </dropdown-link>
                                <dropdown-link
                                    :href="route('invoices.excel.review')"
                                >
                                    {{ __("Review Excel Files") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg5')"
                                    href="#"
                                >
                                    {{ __("Load from ETA") }}
                                </dropdown-link>
                            </template>
                        </dropdown>
                        <dropdown
                            :align="alignDropDown()"
                            width="48"
                            class="ms-3 mb-3 lg:mb-0"
                            v-show="$page.props.e_receipt_enabled"
                        >
                            <template #trigger>
                                <span
                                    class="grid justify-items-center cursor-pointer hover:text-[#4099de]"
                                    :class="{
                                        'text-[#4099de]': invoiceConditions,
                                    }"
                                >
                                    <i class="fa fa-file"></i>
                                    {{ __("Receipts") }}
                                </span>
                            </template>

                            <template #content>
                                <dropdown-link
                                    :href="route('eta.receipts.index')"
                                >
                                    {{ __("Show My Receipts") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('uploadReceipts')"
                                    href="#"
                                >
                                    {{ __("Upload Receipts") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('loadReceipts')"
                                    href="#"
                                >
                                    {{ __("Load from ETA") }}
                                </dropdown-link>
                            </template>
                        </dropdown>
                        <dropdown
                            :align="alignDropDown()"
                            width="48"
                            class="ms-3 mb-3 lg:mb-0"
                        >
                            <template #trigger>
                                <span
                                    class="grid justify-items-center cursor-pointer hover:text-[#4099de]"
                                    :class="{
                                        'text-[#4099de]': itemConditions,
                                    }"
                                >
                                    <i class="fa fa-cube"></i>
                                    {{ __("Items") }}
                                </span>
                            </template>
                            <template #content>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg4')"
                                    href="#"
                                >
                                    {{ __("Add New Item") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg7')"
                                    href="#"
                                >
                                    {{ __("Upload Items") }}
                                </dropdown-link>

                                <dropdown-link
                                    :href="
                                        route('eta.items.index') +
                                        '?page=1&columns%5B0%5D=itemCode&columns%5B1%5D=codeNamePrimaryLang&columns%5B2%5D=parentCodeNameSecondaryLang&columns%5B3%5D=activeTo&columns%5B4%5D=active&columns%5B5%5D=status'
                                    "
                                >
                                    {{ __("Show Items") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg3')"
                                    href="#"
                                >
                                    {{ __("Load from ETA") }}
                                </dropdown-link>

                                <dropdown-link as="a" :href="route('excel.items')">
                                    {{ __("Download") }}
                                </dropdown-link>
                            </template>
                        </dropdown>
                        <dropdown
                            :align="alignDropDown()"
                            width="48"
                            class="ms-3 mb-3 lg:mb-0"
                        >
                            <template #trigger>
                                <span
                                    class="grid justify-items-center cursor-pointer hover:text-[#4099de]"
                                    :class="{
                                        'text-[#4099de]':
                                            $page.url.startsWith('/reports'),
                                    }"
                                >
                                    <i class="fa fa-file-invoice"></i>
                                    {{ __("Reports") }}
                                </span>
                            </template>
                            <template #content>
                                <dropdown-link
                                    :href="route('reports.summary.details')"
                                >
                                    {{ __("Sales Summary") }}
                                </dropdown-link>
                                <dropdown-link
                                    :href="route('reports.summary.purchase')"
                                >
                                    {{ __("Purchase Summary") }}
                                </dropdown-link>
                                <dropdown-link
                                    :href="route('reports.branches.sales')"
                                >
                                    {{ __("Branches Summary") }}
                                </dropdown-link>
                                <dropdown-link
                                    :href="route('reports.customers.sales')"
                                >
                                    {{ __("Customers Summary") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg9')"
                                    href="#"
                                >
                                    {{ __("Invoice Settings") }}
                                </dropdown-link>

                            </template>
                        </dropdown>
                        <dropdown
                            :align="alignDropDown()"
                            width="48"
                            class="ms-3 mb-3 lg:mb-0"
                            v-show="$page.props.e_receipt_enabled"
                        >
                            <template #trigger>
                                <span
                                    class="grid justify-items-center cursor-pointer hover:text-[#4099de]"
                                    :class="{
                                        'text-[#4099de]':
                                            $page.url.startsWith('/pos'),
                                    }"
                                >
                                    <i class="fa fa-cash-register"></i>
                                    {{ __("POS") }}
                                </span>
                            </template>
                            <template #content>
                                <dropdown-link
                                    :href="route('pos.index')"
                                >
                                    {{ __("Show POSs") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('addPOS')"
                                    href="#"
                                >
                                    {{ __("Add POS") }}
                                </dropdown-link>
                            </template>
                        </dropdown>

                        <dropdown
                            :align="alignDropDown()"
                            width="48"
                            class="ms-3 mb-3 lg:mb-0"
                            v-show="$page.props.sales_buzz_enabled"
                        >
                            <template #trigger>
                                <span
                                    class="grid justify-items-center cursor-pointer hover:text-[#4099de]"
                                    :class="{
                                        'text-[#4099de]': invoiceConditions,
                                    }"
                                >
                                    <i class="fa fa-file"></i>
                                    {{ __("Sales Buzz") }}
                                </span>
                            </template>

                            <template #content>
                                <dropdown-link
                                    :href="route('sb.map.index')"
                                >
                                    {{ __("Show Items Map") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg14')"
                                    href="#"
                                >
                                    {{ __("Upload Items Map") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg13')"
                                    href="#"
                                >
                                    {{ __("Load Invoices from Sales Buzz") }}
                                </dropdown-link>
                            </template>
                        </dropdown>

                        <dropdown
                            :align="alignDropDown()"
                            width="48"
                            class="ms-3 mb-3 lg:mb-0"
                        >
                            <template #trigger>
                                <span
                                    class="grid justify-items-center cursor-pointer hover:text-[#4099de]"
                                    :class="{
                                        'text-[#4099de]':
                                            $page.url.startsWith('/archive'),
                                    }"
                                >
                                    <i class="fa fa-box-archive"></i>
                                    {{ __("Archives") }}
                                </span>
                            </template>
                            <template #content>
                                <dropdown-link
                                    :href="route('archive.index')"
                                >
                                    {{ __("Show Archives") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg12')"
                                    href="#"
                                >
                                    {{ __("Request Archive Preparation") }}
                                </dropdown-link>
                                <dropdown-link
                                    :href="route('archive.getArchiveRequests')"
                                >
                                    {{ __("Show Archives (ETA)") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg11')"
                                    href="#"
                                >
                                    {{ __("Request Archive Preparation (ETA)") }}
                                </dropdown-link>
                            </template>
                        </dropdown>
                    </div>

                    <div
                        class="flex flex-col md:flex-row md:items-center -mx-4 md:mx-5"
                    >
                        <language-selector />
                        <dropdown
                            :align="
                                $page.props.locale == 'en' ? 'right' : 'left'
                            "
                            class="ms-3"
                        >
                            <template #trigger>
                                <span
                                    class="cursor-pointer hover:text-blue-600"
                                >
                                    <i class="fa fa-user"></i>
                                    {{ $page.props.user.name }}
                                </span>
                            </template>
                            <template #content>
                                <dropdown-link :href="route('profile.show')">
                                    {{ __("Profile") }}
                                </dropdown-link>

                                <dropdown-link
                                    :href="route('api-tokens.index')"
                                    v-if="$page.props.user.id == 1"
                                >
                                    {{ __("API Tokens") }}
                                </dropdown-link>

                                <dropdown-link
                                    :href="route('users.index')"
                                    v-if="$page.props.user.id == 1"
                                >
                                    {{ __("Users") }}
                                </dropdown-link>
                                 <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg10')"
                                    v-if="$page.props.user.id == 1"
                                    href="#"
                                >
                                    {{ __("Settings") }}
                                </dropdown-link>

                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg('dlg8')"
                                    href="#"
                                >
                                    {{ __("Add New User") }}
                                </dropdown-link>
                                <div class="border-t border-gray-100"></div>
                                <form @submit.prevent="logout">
                                    <dropdown-link as="button">
                                        <i class="fas fa-sign-out-alt"></i>
                                        {{ __("Log Out") }}
                                    </dropdown-link>
                                </form>
                            </template>
                        </dropdown>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import LanguageSelector from "@/Language/LanguageSelector";
import Dropdown from "@/Jetstream/Dropdown";
import DropdownLink from "@/Jetstream/DropdownLink";
import JetApplicationMark from "@/Jetstream/ApplicationMark";

export default {
    emits: [
        "open_dlg"
    ],
    components: {
        Link,
        LanguageSelector,
        Dropdown,
        DropdownLink,
        JetApplicationMark,
    },
    data() {
        return {
            isOpen: false
        };
    },
    methods: {
        logout() {
            axios.post(route("logout")).then((res) => {
                window.location.reload();
            });
        },
        alignDropDown() {
            return this.$page.props.locale == "en" ? "left" : "right";
        },
        openDlg($dlg) {
            this.$emit("open_dlg", $dlg);
        },
        openDlg12() {
            this.$emit("open:dlg12");
        },
        openDlg12() {
            this.$emit("open:dlg12");
        },
    },
    computed: {
        invoiceConditions() {
            return (
                this.$page.url.startsWith("/invoices") ||
                this.$page.url.startsWith("/ETA/Invoices")
            );
        },
        itemConditions() {
            return this.$page.url.startsWith("/ETA/Items");
        },
    },
};
</script>
