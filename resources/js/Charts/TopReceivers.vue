<template>
	<v-chart class="chart" :option="option" />
</template>

<script>
	import { use } from "echarts/core";
	import { CanvasRenderer } from "echarts/renderers";
	import { FunnelChart, BarChart, SankeyChart, PieChart, TreemapChart } from "echarts/charts";
	import {
		TitleComponent,
		TooltipComponent,
		LegendComponent
	} from "echarts/components";
	import VChart, { THEME_KEY } from "vue-echarts";
	import { ref, defineComponent } from "vue";
	import axios from 'axios';
	
	use([
		CanvasRenderer,
		PieChart,
		FunnelChart,
		BarChart,
		SankeyChart,
		TitleComponent,
		TooltipComponent,
		TreemapChart,
		LegendComponent
	]);

	export default defineComponent({
		name: "HelloWorld",
		components: {
			VChart,
		},
		props: ['coursenum'],
		data: function() {
            return {
				option: {
					title: {
					 	text: "Top Receivers (Customers)",
					 	left: "center"
					},
					tooltip: {
						trigger: 'item'
					},
					legend: {
						top: '10%',
						left: 'left',
						orient: "vertical"
					},
					series: [
						{
							type: 'pie',
							left: '30%',
							radius: ['30%', '80%'],
								avoidLabelOverlap: false,
								itemStyle: {
									borderRadius: 10,
									borderColor: '#fff',
									borderWidth: 2
								},
								label: {
									show: false,
									position: 'center'
								},
								emphasis: {
									label: {
										show: false,
										fontSize: '20',
										fontWeight: 'bold'
									}
								},
								labelLine: {
									show: false
								},
							data: [
								  {"name": "Total"},
								  {"name": "Part1"},
								  {"name": "Part2"}
							],
							
							}
					]
				}
            }
        },
		methods: {
		},
		created: function created() {
			axios.get(route('json.top.receivers'))
			.then(response => {
				this.option.series[0].data = response.data;
            }).catch(error => {
		   	});
		},
	});
</script>

<style scoped>
.chart {
	height: 300px;
}
</style>

