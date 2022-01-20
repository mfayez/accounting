<template>
    <div>
        <jet-banner />
		<new-customer-dialog ref="dlg1"/>
		<new-branch-dialog ref="dlg2"/>
		<load-items-dialog ref="dlg3"/>
		<new-item-dialog ref="dlg4"/>
		<load-invoices-dialog ref="dlg5"/>
		<upload-invoices-dialog ref="dlg6" />
		<upload-items-dialog ref="dlg7" />
		<new-user-dialog ref="dlg8" />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex sm:items-center">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <inertia-link :href="route('dashboard')">
                                    <jet-application-mark class="block h-9 w-auto" />
                                </inertia-link>
                            </div>

                            <!-- Navigation Links -->
                            <!--
							<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <jet-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </jet-nav-link>
                            </div>
							-->
							<!-- Settings Dropdown -->
							<div class="ms-3 relative">
                                <inertia-link :href="route('dashboard')">
									<div>Invoice Master</div>
                                </inertia-link>
							</div>
                            <div class="ms-3 relative">
                                <jet-dropdown align="left" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                {{ $page.props.user.name }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <!--
										<div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>
										-->

                                        <jet-dropdown-link :href="route('profile.show')">
                                            {{ __('Profile')}}
                                        </jet-dropdown-link>

                                        <jet-dropdown-link :href="route('api-tokens.index')" v-if="$page.props.user.id == 1">
                                            {{ __('API Tokens')}}
                                        </jet-dropdown-link>

                                        <jet-dropdown-link :href="route('users.index')" v-if="$page.props.user.id == 1">
                                            {{ __('Users')}}
                                        </jet-dropdown-link>
                                        
										<jet-dropdown-link as="a" @click.prevent="openDlg8()" href="#">
                                            {{ __('Add New User') }}
                                        </jet-dropdown-link>

                                        <div class="border-t border-gray-100"></div>

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <jet-dropdown-link as="button">
                                                {{ __('Log Out')}}
                                            </jet-dropdown-link>
                                        </form>
                                    </template>
                                </jet-dropdown>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            
							<!-- Customers Dropbown -->
							<div class="ms-3 relative">
                                <jet-dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                {{ __('Customers') }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <jet-dropdown-link as="a" @click.prevent="openDlg1()" :href="route('customers.create')">
                                            {{ __('Add New Customer') }}
                                        </jet-dropdown-link>
										
										<jet-dropdown-link as="a" :href="route('customers.index')">
                                            {{ __('Show Customer') }}
                                        </jet-dropdown-link>
										
                                    </template>
                                </jet-dropdown>
                            </div>
							<!-- Branches Dropbown -->
							<div class="ms-3 relative">
                                <jet-dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                {{ __('Branches') }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <jet-dropdown-link as="a" @click.prevent="openDlg2()" href="#">
                                            {{ __('Add New Branch') }}
                                        </jet-dropdown-link>
										
										<jet-dropdown-link as="a" :href="route('branches.index')">
                                            {{ __('Show Branches') }}
                                        </jet-dropdown-link>
										
                                    </template>
                                </jet-dropdown>
                            </div>

							<!-- Invoices Dropbown -->
							<div class="ms-3 relative">
                                <jet-dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                {{ __('Invoices') }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <jet-dropdown-link as="a" :href="route('invoices.create')">
                                            {{ __('Add New Invoice') }}
                                        </jet-dropdown-link>
										<jet-dropdown-link as="a" :href="route('eta.invoices.sent.index')">
                                            {{ __('Show My Invoices') }}
                                        </jet-dropdown-link>

										<jet-dropdown-link as="a" :href="route('eta.invoices.received.index')">
                                            {{ __('Show Paid Invoices') }}
                                        </jet-dropdown-link>
										<jet-dropdown-link as="a" @click.prevent="openDlg6()" href="#"> 
                                            {{ __('Upload Invoices') }}
                                        </jet-dropdown-link>
                                        <jet-dropdown-link as="a" :href="route('invoices.excel.review')">
                                            {{ __('Review Excel Files') }}
                                        </jet-dropdown-link>
                                        <jet-dropdown-link as="a" @click.prevent="openDlg5()" href="#">
                                            {{ __('Load from ETA') }}
                                        </jet-dropdown-link>
										
                                    </template>
                                </jet-dropdown>
                            </div>
							<!-- Items Dropbown -->
							<div class="ms-3 relative">
                                <jet-dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                {{ __('Items') }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <jet-dropdown-link as="a" @click.prevent="openDlg4()" href="#">
                                            {{ __('Add New Item') }}
                                        </jet-dropdown-link>
										<jet-dropdown-link as="a" @click.prevent="openDlg7()" href="#"> 
                                            {{ __('Upload Items') }}
                                        </jet-dropdown-link>
										
										<jet-dropdown-link as="a" :href="route('eta.items.index')+'?page=1&columns%5B0%5D=itemCode&columns%5B1%5D=codeNamePrimaryLang&columns%5B2%5D=parentCodeNameSecondaryLang&columns%5B3%5D=activeTo&columns%5B4%5D=active&columns%5B5%5D=status'">
                                            {{ __('Show Items') }}
                                        </jet-dropdown-link>
                                        <jet-dropdown-link as="a" @click.prevent="openDlg3()" href="#">
                                            {{ __('Load from ETA') }}
                                        </jet-dropdown-link>
										
                                    </template>
                                </jet-dropdown>
                            </div>
							<!--Reports-->
							<div class="ms-3 relative">
                                <jet-dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                {{ __('Reports') }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
										<jet-dropdown-link as="a" :href="route('reports.summary.details')">
                                            {{ __('Detailed Summary') }}
                                        </jet-dropdown-link>
                                    </template>
                                </jet-dropdown>
                            </div>

							
							<language-selector />
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex-shrink-0 me-3" >
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">{{ $page.props.user.name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ $page.props.user.email }}</div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <jet-responsive-nav-link :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </jet-responsive-nav-link>

                            <jet-responsive-nav-link :href="route('api-tokens.index')" :active="route().current('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                                API Tokens
                            </jet-responsive-nav-link>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <jet-responsive-nav-link as="button">
                                    Log Out
                                </jet-responsive-nav-link>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header"></slot>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot></slot>
            </main>
        </div>
    </div>
</template>

<script>
    import JetApplicationMark from '@/Jetstream/ApplicationMark'
    import JetBanner from '@/Jetstream/Banner'
    import JetDropdown from '@/Jetstream/Dropdown'
    import JetDropdownLink from '@/Jetstream/DropdownLink'
    import JetNavLink from '@/Jetstream/NavLink'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
	import LanguageSelector from '@/Language/LanguageSelector'
	import NewCustomerDialog from '@/Pages/Customers/Edit'
	import NewBranchDialog from '@/Pages/Branches/Edit'
	import NewUserDialog from '@/Pages/Users/Edit'
	import LoadItemsDialog from '@/Pages/Items/Load'
	import LoadInvoicesDialog from '@/Pages/Invoices/Load'
	import NewItemDialog from '@/Pages/Items/Edit'
	import UploadInvoicesDialog from '@/Pages/Invoices/Upload'
	import UploadItemsDialog from '@/Pages/Items/Upload'

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
            JetApplicationMark,
            JetBanner,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            JetResponsiveNavLink,
			LanguageSelector,
        },
		
		data() {
            return {
                showingNavigationDropdown: false,
            }
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
            logout() {
                this.$inertia.post(route('logout'));
            },
        }
    }
</script>
