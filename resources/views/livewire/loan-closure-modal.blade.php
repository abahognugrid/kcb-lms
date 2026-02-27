<div>
    <!-- Modal Backdrop -->
    @if ($showModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black opacity-50" wire:click="closeModal"></div>

            <!-- Modal Container -->
            <div class="relative w-auto max-w-md mx-auto my-6">
                <!-- Modal Content -->
                <div class="relative bg-white rounded-lg shadow-lg">
                    <!-- Header -->
                    <div class="flex items-start justify-between p-5 border-b border-gray-200 rounded-t">
                        <h3 class="text-lg font-semibold text-gray-900">Loan Closure</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                            <span class="text-2xl">&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-6">
                        <form wire:submit.prevent="submit">
                            <div class="mb-4">
                                <label for="disposalIncome"
                                    class="block mb-2 text-sm font-medium text-gray-700">Disposal Income</label>
                                <input wire:model="disposalIncome" type="number" step="0.01" id="disposalIncome"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('disposalIncome')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="disposalCosts" class="block mb-2 text-sm font-medium text-gray-700">Disposal
                                    Costs</label>
                                <input wire:model="disposalCosts" type="number" step="0.01" id="disposalCosts"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('disposalCosts')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="outstandingBalance"
                                    class="block mb-2 text-sm font-medium text-gray-700">Outstanding Balance</label>
                                <input wire:model="outstandingBalance" type="number" step="0.01"
                                    id="outstandingBalance"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    readonly>
                            </div>

                            <!-- Footer -->
                            <div class="flex items-center justify-end p-6 border-t border-gray-200 rounded-b">
                                <button type="button" wire:click="closeModal"
                                    class="px-4 py-2 mr-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
