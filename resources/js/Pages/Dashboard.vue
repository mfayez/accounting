<template>
    <app-layout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
					<div class="container">
						<!-- Column -->
						<div class="my-1 px-1 w-full md:w-1/2 lg:my-4 lg:px-4 lg:w-1/3">
							<!-- Article -->
							<article class="overflow-hidden rounded-lg shadow-lg">
								<header class="flex items-center justify-between leading-tight p-2 md:p-4">
									<h1 class="text-lg">
										<a class="no-underline hover:underline text-black" href="#">
											Issued Invoices Statistics
										</a>
									</h1>
									<p class="text-grey-darker text-sm">
										11/1/19
									</p>
								</header>
								<div class="p-4">Invoices Statistics</div>

								<footer class="flex items-center justify-between leading-none p-2 md:p-4">
								</footer>

							</article>
							<!-- END Article -->

						</div>
						<!-- END Column -->

					</div>
					<img src="images\placeholder.png" />
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Welcome from '@/Jetstream/Welcome'

    export default {
        components: {
            AppLayout,
            Welcome,
        },
    }
</script>
