<template>
    <nav class="bg-white shadow dark:bg-gray-800">
        <div class="container px-6 py-4 mx-auto">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex items-center justify-between">
                    <div class="text-xl font-semibold text-gray-700">
                        <Link :href="route('dashboard')" class="flex">
                            <jet-application-mark class="w-10"/>
                            <span class="self-center ltr:ml-2 rtl:mr-2"
                                >Invoice Master</span
                            >
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
                        <Link :href="route('dashboard')" :class="{'text-[#4099de]': $page.url === '/'}"><i class="fas fa-chart-pie"></i> Dashboard</Link>
                        <dropdown
                            :align="alignDropDown()"
                            class="ltr:ml-3 rtl:mr-3 mb-3 lg:mb-0"
                        >
                            <template #trigger>
                                <span class="cursor-pointer hover:text-[#4099de]" :class="{'text-[#4099de]': $page.url.startsWith('/customers')}">
                                    <i class="fa fa-user-tie"></i>
                                    {{ __("Customers") }}
                                </span>
                            </template>
                            <template #content>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg1()"
                                    :href="route('customers.create')"
                                >
                                    {{ __("Add New Customer") }}
                                </dropdown-link>

                                <dropdown-link :href="route('customers.index')">
                                    {{ __("Show Customer") }}
                                </dropdown-link>
                            </template>
                        </dropdown>
                        <dropdown
                            :align="alignDropDown()"
                            width="48"
                            class="ltr:ml-3 rtl:mr-3 mb-3 lg:mb-0"
                        >
                            <template #trigger>
                                <span class="cursor-pointer hover:text-[#4099de]" :class="{'text-[#4099de]': $page.url.startsWith('/branches')}">
                                    <i class="fa fa-code-branch"></i>
                                    {{ __("Branches") }}
                                </span>
                            </template>

                            <template #content>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg2()"
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
                            class="ltr:ml-3 rtl:mr-3 mb-3 lg:mb-0"
                        >
                            <template #trigger>
                                <span class="cursor-pointer hover:text-[#4099de]" :class="{'text-[#4099de]': invoiceConditions}">
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
                                    @click.prevent="openDlg6()"
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
                                    @click.prevent="openDlg5()"
                                    href="#"
                                >
                                    {{ __("Load from ETA") }}
                                </dropdown-link>
                            </template>
                        </dropdown>
                        <dropdown
                            :align="alignDropDown()"
                            width="48"
                            class="ltr:ml-3 rtl:mr-3 mb-3 lg:mb-0"
                        >
                            <template #trigger>
                                <span class="cursor-pointer hover:text-[#4099de]" :class="{'text-[#4099de]': itemConditions}">
                                    <i class="fa fa-cube"></i>
                                    {{ __("Items") }}
                                </span>
                            </template>
                            <template #content>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg4()"
                                    href="#"
                                >
                                    {{ __("Add New Item") }}
                                </dropdown-link>
                                <dropdown-link
                                    as="a"
                                    @click.prevent="openDlg7()"
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
                                    @click.prevent="openDlg3()"
                                    href="#"
                                >
                                    {{ __("Load from ETA") }}
                                </dropdown-link>
                            </template>
                        </dropdown>
                        <dropdown
                            :align="alignDropDown()"
                            width="48"
                            class="ltr:ml-3 rtl:mr-3 mb-3 lg:mb-0"
                        >
                            <template #trigger>
                                <span class="cursor-pointer hover:text-[#4099de]" :class="{'text-[#4099de]': $page.url.startsWith('/reports')}">
                                    <i class="fa fa-file-invoice"></i>
                                    {{ __("Reports") }}
                                </span>
                            </template>
                            <template #content>
                                <dropdown-link
                                    :href="route('reports.summary.details')"
                                >
                                    {{ __("Detailed Summary") }}
                                </dropdown-link>
                            </template>
                        </dropdown>
                    </div>

                    <div
                        class="flex flex-col md:flex-row md:items-center -mx-4 md:mx-5"
                    >
                        <language-selector />
                        <dropdown
                            :align="alignDropDown()"
                            class="ltr:ml-3 rtl:mr-3"
                        >
                            <template #trigger>
                                <span class="cursor-pointer hover:text-blue-600">
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
                                    @click.prevent="openDlg8()"
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
        "open:dlg1",
        "open:dlg2",
        "open:dlg3",
        "open:dlg4",
        "open:dlg5",
        "open:dlg6",
        "open:dlg7",
        "open:dlg8",
    ],
    data() {
        return {
            isOpen: false,
        };
    },
    components: {
        Link,
        LanguageSelector,
        Dropdown,
        DropdownLink,
        JetApplicationMark,
    },
    methods: {
        logout() {
            axios.post(route("logout")).then((res) => {
                window.location.reload();
            });
        },
        alignDropDown() {
            return this.$page.props.locale == "en" ? "right" : "left";
        },
        openDlg1() {
            this.$emit("open:dlg1");
        },
        openDlg2() {
            this.$emit("open:dlg2");
        },
        openDlg3() {
            this.$emit("open:dlg3");
        },
        openDlg4() {
            this.$emit("open:dlg4");
        },
        openDlg5() {
            this.$emit("open:dlg5");
        },
        openDlg6() {
            this.$emit("open:dlg6");
        },
        openDlg7() {
            this.$emit("open:dlg7");
        },
        openDlg8() {
            this.$emit("open:dlg8");
        },
    },
    computed: {
        invoiceConditions() {
            return this.$page.url.startsWith('/invoices') || this.$page.url.startsWith('/ETA/Invoices');
        },
        itemConditions() {
            return this.$page.url.startsWith('/ETA/Items');
        }
    }
};
</script>
