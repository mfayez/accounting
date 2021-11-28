<template>
    <app-layout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

<div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-4 sm:px-8">
	<div class="items-center bg-white border rounded-sm overflow-hidden shadow" v-for="row in statistics">
		<div class="uppercase text-center text-3xl p-4 w-full" :class="row.Status">
			{{row.Status}}			
		</div>
		<div class="px-4 text-gray-700">
			<p class="text-xl">Invoices: {{row.invoicesCount}}</p>
			<p class="text-xl">Net Amount: {{row.totalSalesAmount}}</p>
			<p class="text-xl">Gross Amount: {{row.totalAmount}}</p>
			<p class="text-xl">Tax Amount: {{row.taxTotal}}</p>
		</div>
	</div>
</div>
<div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:px-8">
	<top-items />
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

    export default {
        components: {
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
.processing {
	background-color: #0E9F6E
}
.rejected {
	background-color: #F05252
}
</style>
