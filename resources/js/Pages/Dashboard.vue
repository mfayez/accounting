<template>
    <app-layout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

<div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-4 sm:px-8">
	<div class="items-center bg-white border rounded-sm overflow-hidden shadow" v-for="row in statistics">
		<div class="uppercase text-center text-3xl p-4 w-full" :class="row.Status">
			{{__(row.Status)}}			
		</div>
		<div class="px-4 text-gray-700">
			<p class="text-xl">{{__('Invoices')}}: {{row.invoicesCount}}</p>
			<p class="text-xl">{{__('Net Amount')}}: {{row.totalSalesAmount}}</p>
			<p class="text-xl">{{__('Gross Amount')}}: {{row.totalAmount}}</p>
			<p class="text-xl">{{__('Tax Amount')}}: {{row.taxTotal}}</p>
		</div>
	</div>
</div>
<div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:px-8">
	<top-items />
	<top-receivers />
</div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Welcome from '@/Jetstream/Welcome'
	import TopItems from '@/Charts/TopItems'
	import TopReceivers from '@/Charts/TopReceivers'

    export default {
        components: {
			TopReceivers,
			TopItems,
            AppLayout,
            Welcome,
        },
		props: {
			statistics: Object
		}
    }
</script>

<style>
.Valid {
	background-color: #0E9F6E
}
.processing {
	background-color: #0E9F6E
}
.Review {
	background-color: #0E6E9F
}
.Canceled {
	background-color: #9F0E6E
}
.Invalid {
	background-color: #F05252
}
.rejected {
	background-color: #F05252
}
</style>
