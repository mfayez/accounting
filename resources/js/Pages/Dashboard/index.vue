<template>
    <app-layout>
        <statistics-table
            :title="__('Last 7 days Invoices Statistics')"
            :from="firstDayOfLastWeek"
            :to="today"
            :data="lastWeekData"
        />
        <statistics-table
            :title="__('Monthly Invoices Statistics')"
            :from="firstDayOfTheCurrentMonth"
            :to="lastDayOfTheCurrentMonth"
            :data="monthlyData"
        />
        <statistics-table
            :title="__('Last Month Invoices Statistics')"
            :from="firstDayOfTheLastMonth"
            :to="lastDayOfTheLastMonth"
            :data="lastMonthData"
        />
        <charts />
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import charts from "./charts";
import StatisticsTable from "./StatisticsTable.vue";
export default {
    components: {
        AppLayout,
        charts,
        StatisticsTable,
    },
    props: {
        statistics: Object,
        firstDayOfLastWeek: String,
        today: String,
        firstDayOfTheCurrentMonth: String,
        lastDayOfTheCurrentMonth: String,
        firstDayOfTheLastMonth: String,
        lastDayOfTheLastMonth: String,
        lastWeekData: Array,
        lastMonthData: Array,
        monthlyData: Array,
    },
};
</script>

<style>
.Valid {
    background-color: #0e9f6e;
}
.processing {
    background-color: #0e9f6e;
}
.Review {
    background-color: #0e6e9f;
}
.Canceled {
    background-color: #9f0e6e;
}
.Invalid {
    background-color: #f05252;
}
.rejected {
    background-color: #f05252;
}
</style>
