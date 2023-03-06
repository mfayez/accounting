<template>
    <!-- prettier-ignore -->
    <div class="w-full lg:w-2/4">
       <div class="flex flex-wrap mb-3">
            <div
                class="flex items-center mr-3 mb-3 w-1/5"
                v-for="row in statusList"
                :key="row"
            >
                <input
                    :id="row"
                    name="remember-me"
                    type="checkbox"
                    :value="row"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    @change="getDataBaseOnStatus"
                    v-model="selectedStatus"
                />
                <label
                    :for="row"
                    class="ltr:ml-2 rtl:mr-2 block text-sm text-gray-900 capitalize"
                >
                    {{ __(row) }}
                </label>
            </div>
        </div>
        <v-chart class="chart" :option="option" />
    </div>
</template>

<script>
import { use } from "echarts/core";

import { CanvasRenderer } from "echarts/renderers";

import { PieChart } from "echarts/charts";

import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
} from "echarts/components";

import VChart, { THEME_KEY } from "vue-echarts";

import { ref, defineComponent } from "vue";
import axios from 'axios';

use([
    CanvasRenderer,
    PieChart,
    TitleComponent,
    TooltipComponent,
    LegendComponent,
]);

export default defineComponent({
    name: "TopReceiversByStatus",
    components: {
        VChart,
    },
    props: ["coursenum", "title"],
    data: function () {
        return {
            option: {
                title: {
                    text: this.title,
                    left: "center",
                },
                tooltip: {
                    trigger: "item",
                },
                legend: {
                    top: "10%",
                    left: "left",
                    orient: "vertical",
                },
                series: [
                    {
                        type: "pie",
                        left: "20%",
                        radius: ["30%", "80%"],
                        avoidLabelOverlap: false,
                        itemStyle: {
                            borderRadius: 10,
                            borderColor: "#fff",
                            borderWidth: 2,
                        },
                        label: {
                            show: false,
                            position: "center",
                        },
                        emphasis: {
                            label: {
                                show: false,
                                fontSize: "20",
                                fontWeight: "bold",
                            },
                        },
                        labelLine: {
                            show: false,
                        },
                        data: [
                            { name: "Total" },
                            { name: "Part1" },
                            { name: "Part2" },
                        ],
                    },
                ],
            },
            statusList: [
                "pending",
                "processing",
                "Valid",
                "review",
                "In Review",
                "Invalid",
                "Canceled",
                "rejected",
            ],
            selectedStatus: [],
        };
    },
    methods: {
        getDataBaseOnStatus: async function () {
            try {
                const res = await axios.post(route("json.top.receivers"), {
                    statusList: this.selectedStatus,
                });
                this.option.series[0].data = res.data;
            } catch (error) {
                console.error(error);
            }
        },
    },
    created: function created() {
        axios
            .post(route("json.top.receivers"))
            .then((response) => {
                this.option.series[0].data = response.data;
            })
            .catch((error) => {});
    },
});
</script>

<style scoped>
.chart {
    height: 300px;
}
</style>
