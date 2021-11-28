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
					// title: {
					// 	text: "Skills matched with top 20 jobs in the market",
					// 	left: "center"
					// },
					tooltip: {
						trigger: 'item'
					},
					legend: {
						top: '1%',
						left: 'left',
						orient: "vertical"
					},
					series: [
						{
							type: 'pie',
							left: '20%',
							radius: ['70%', '100%'],
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
										show: true,
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
			axios.get(route('json.top.items'))
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

