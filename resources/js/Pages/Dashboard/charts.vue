<template>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="flex flex-col lg:flex-row justify-between p-4">
                    <top-items-by-status :title="__('Top Items')" />
                    <top-receivers-by-status
                        :title="__('Top Receivers (Customers)')"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import TopItemsByStatus from "@/Charts/TopItemsByStatus";
import TopReceiversByStatus from "@/Charts/TopReceiversByStatus";
export default {
    components: {
        TopReceiversByStatus,
        TopItemsByStatus,
    },
};
</script>
