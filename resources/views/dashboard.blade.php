<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>

            <div class="absolute left-1/2 transform -translate-x-1/2 flex items-center space-x-2 bg-green-50 border border-green-200 px-4 py-1.5 rounded-full shadow-sm">
                <span class="text-lg">💰</span>
                <span class="text-xs text-green-700 font-bold uppercase tracking-wider">Balance:</span>
                <span class="text-lg font-black text-green-600">${{ $balance }}</span>
            </div>
            <div class="w-10"></div>
        </div>
    </x-slot>

    <div class="py-12" 
         id="dashboard-root"
         data-mother-services="{{ json_encode(App\Models\Service::whereNull('parent_id')->get(['id', 'name', 'price', 'description'])) }}"
         data-client-token="{{ $clientToken }}"
         data-history-url="{{ route('transaction.history') }}"
         data-store-url="{{ route('transaction.store') }}"
         data-purchase-url="{{ route('services.purchase') }}" 
         data-csrf="{{ csrf_token() }}"
         x-data="walletDashboardComponent()"
         x-init="initComponent()">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 text-gray-900">
                    <p class="text-gray-500 text-sm">Welcome back! Quick manage your wallet from here.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <button type="button" @click="toggleForm('deposit')" 
                                :class="activeForm === 'deposit' ? 'ring-4 ring-emerald-200 bg-emerald-700' : 'bg-emerald-600'"
                                class="flex items-center justify-center space-x-2 text-white font-bold py-3 px-6 rounded-xl shadow-md hover:bg-emerald-700 transition">
                            <span>➕ Deposit Money</span>
                        </button>

                        <button type="button" @click="toggleForm('withdraw')" 
                                :class="activeForm === 'withdraw' ? 'ring-4 ring-rose-200 bg-rose-700' : 'bg-rose-600'"
                                class="flex items-center justify-center space-x-2 text-white font-bold py-3 px-6 rounded-xl shadow-md hover:bg-rose-700 transition">
                            <span>➖ Withdraw Money</span>
                        </button>
                    </div>

                    <div x-show="activeForm !== null" x-collapse class="mt-6 p-6 border border-gray-100 rounded-2xl bg-gray-50/50" style="display: none;">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-gray-800 capitalize" x-text="activeForm + ' Funds'"></h4>
                            <button type="button" @click="activeForm = null" class="text-gray-400 hover:text-gray-600 text-sm font-bold">✖ Close</button>
                        </div>

                        <form @submit.prevent="submitTransaction">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-xs font-bold mb-2">Amount (USD)</label>
                                <input type="number" min="1" x-model="amount" class="w-full border-gray-300 focus:ring-indigo-500 rounded-xl py-2.5 px-3 text-sm" placeholder="0.00" required>
                            </div>

                            <div x-show="activeForm === 'deposit'" class="mb-4" x-unget>
                                <div id="dashboard-dropin-container"></div>
                            </div>

                            <div x-show="txError" x-text="txError" class="mb-3 p-3 bg-rose-50 text-rose-600 rounded-xl text-xs font-medium" style="display: none;"></div>

                            <button type="submit" :disabled="isTxProcessing" 
                                    :class="activeForm === 'deposit' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'"
                                    class="w-full text-white font-bold py-2.5 px-4 rounded-xl shadow transition text-sm flex justify-center items-center">
                                <span x-show="!isTxProcessing" x-text="'Confirm ' + activeForm"></span>
                                <span x-show="isTxProcessing" style="display: none;">⏳ Processing...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 col-span-1">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2 text-lg">📁 Main Services</h3>
                    <div class="space-y-2">
                        <template x-for="mother in motherServices" :key="mother.id">
                            <button type="button" @click="fetchChildren(mother.id, mother.name)"
                                    :class="selectedMotherId === mother.id ? 'bg-indigo-600 text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                                    class="w-full text-left px-4 py-3 rounded-lg font-medium transition flex justify-between items-center shadow-sm">
                                <span x-text="mother.name"></span>
                                <span x-show="selectedMotherId === mother.id">➡️</span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 col-span-2">
                    <div x-show="!selectedMotherId" class="text-center py-12 text-gray-400">
                        <span class="text-4xl block mb-2">🖱️</span>
                        <p>Select a main service to view its sub-services.</p>
                    </div>
                    <div x-show="isLoadingServices" class="text-center py-12 text-indigo-500 font-semibold" style="display: none;">🔄 Loading sub-services...</div>
                    
                    <div x-show="selectedMotherId && !isLoadingServices" style="display: none;">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 text-lg">Sub-services for: <span class="text-indigo-600" x-text="selectedMotherName"></span></h3>
                        <div x-show="childrenServices.length === 0" class="text-gray-500 py-6 text-center bg-gray-50 rounded-lg">No sub-services available.</div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="childrenServices.length > 0">
                            <template x-for="child in childrenServices" :key="child.id">
                                <div class="border border-gray-200 p-4 rounded-xl relative shadow-sm bg-stone-50/50">
                                    <h4 class="font-bold text-gray-800 text-base" x-text="child.name"></h4>
                                    <p class="text-gray-500 text-xs mt-1" x-text="child.description || 'No description available'"></p>
                                    <div class="mt-4 flex justify-between items-center">
                                        <span class="text-green-600 font-extrabold text-sm" x-text="'$' + child.price"></span>
                                        <button type="button" 
                                                @click="buyService(child.id)" 
                                                :disabled="isBuying === child.id"
                                                class="bg-indigo-50 text-indigo-600 text-xs px-3 py-1.5 rounded-md font-bold hover:bg-indigo-600 hover:text-white transition flex items-center space-x-1">
                                            <span x-show="isBuying !== child.id">Select ⚡</span>
                                            <span x-show="isBuying === child.id" style="display: none;">⏳ ...</span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-700 flex items-center space-x-2">📜 <span>Recent Transactions</span></h3>
                    <span class="text-xs bg-gray-100 text-gray-600 font-bold px-2.5 py-1 rounded-md" x-text="transactions.length + ' Total'"></span>
                </div>
                <div class="p-6">
                    <div x-show="isLoadingHistory" class="text-center py-6 text-gray-400 flex flex-col items-center justify-center"><span class="animate-spin text-xl">🔄</span></div>
                    <div x-show="!isLoadingHistory && transactions.length === 0" class="text-center py-8 text-gray-400 text-sm" style="display: none;">No history records found.</div>
                    
                    <div class="overflow-x-auto rounded-xl border border-gray-100" x-show="!isLoadingHistory && transactions.length > 0" style="display: none;">
                        <table class="min-w-full divide-y divide-gray-100 text-left text-xs">
                            <thead class="bg-gray-50 text-gray-500 font-bold uppercase">
                                <tr>
                                    <th class="px-6 py-3">Reference</th>
                                    <th class="px-6 py-3">Type</th>
                                    <th class="px-6 py-3">Amount</th>
                                    <th class="px-6 py-3">Currency</th>
                                    <th class="px-6 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                                <template x-for="tx in transactions" :key="tx.id">
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-3.5 font-mono text-gray-400" x-text="'#' + tx.id"></td>
                                        <td class="px-6 py-3.5">
                                            <span :class="tx.type === 'deposit' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold capitalize"><span x-text="tx.type"></span></span>
                                        </td>
                                        <td class="px-6 py-3.5 font-bold" :class="tx.type === 'deposit' ? 'text-emerald-600' : 'text-rose-600'" x-text="(tx.type === 'deposit' ? '+' : '-') + ' $' + parseFloat(tx.amount).toFixed(2)"></td>
                                        <td class="px-6 py-3.5 uppercase text-gray-400 font-bold" x-text="tx.currency"></td>
                                        <td class="px-6 py-3.5 text-gray-400" x-text="new Date(tx.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://js.braintreegateway.com/web/dropin/1.43.0/js/dropin.min.js"></script>
    
    <script>
        function walletDashboardComponent() {
            const rootEl = document.getElementById('dashboard-root');
            
            return {
                motherServices: JSON.parse(rootEl.getAttribute('data-mother-services')),
                clientToken: rootEl.getAttribute('data-client-token'),
                historyUrl: rootEl.getAttribute('data-history-url'),
                storeUrl: rootEl.getAttribute('data-store-url'),
                purchaseUrl: rootEl.getAttribute('data-purchase-url'), 
                csrfToken: rootEl.getAttribute('data-csrf'),
                
                selectedMotherId: null,
                selectedMotherName: '',
                childrenServices: [],
                isLoadingServices: false,
                transactions: [],
                isLoadingHistory: true,
                isBuying: null, 
                
                activeForm: null,
                amount: '',
                txError: '',
                isTxProcessing: false,
                braintreeInstance: null,

                initComponent() {
                    this.fetchHistory();
                },

                toggleForm(type) {
                    if (this.activeForm === type) {
                        this.activeForm = null;
                        return;
                    }
                    this.activeForm = type;
                    this.amount = '';
                    this.txError = '';

                    if (type === 'deposit') {
                        this.$nextTick(() => {
                            if (this.braintreeInstance) {
                                this.braintreeInstance.destroy().then(() => {
                                    this.braintreeInstance = null;
                                    this.initBraintree();
                                }).catch(e => console.error(e));
                            } else {
                                this.initBraintree();
                            }
                        });
                    }
                },

                initBraintree() {
                    if (!this.clientToken || this.clientToken.trim() === "") {
                        this.txError = "Gateway Error: Client token is missing.";
                        return;
                    }

                    braintree.dropin.create({
                        authorization: this.clientToken,
                        container: '#dashboard-dropin-container',
                        paypal: { flow: 'vault' },
                        card: {
                            cardholderNameSetting: { required: false },
                            overrides: { fields: { postalCode: false } }
                        }
                    }, (err, instance) => {
                        if (err) { 
                            console.error("Braintree error:", err);
                            this.txError = 'Gateway Error: ' + err.message; 
                            return; 
                        }
                        this.braintreeInstance = instance;
                    });
                },

                // 🔥 DYNAMIC FIXED PURCHASE HANDLER
                async buyService(serviceId) {
                   
                    if (!confirm("Are you sure you want to purchase this service?")) return;
                    
                    this.isBuying = serviceId;
                    try {
                        let response = await fetch(this.purchaseUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                            },
                            // 🔥 clean object payload match 100% with your controller store validation rule
                            body: JSON.stringify({ 
                                service_id: serviceId 
                            })
                        });
                        
                        let data = await response.json();
                        
                        if (response.ok) {
                            alert(data.message || 'Purchase complete!');
                            window.location.reload(); 
                        } else {
                            alert(data.message || 'Something went wrong.');
                        }
                    } catch (error) {
                        console.error(error);
                        alert('Server Error. Please try again.');
                    } finally {
                        this.isBuying = null;
                    }
                },

                async fetchChildren(motherId, motherName) {
                    this.selectedMotherId = motherId;
                    this.selectedMotherName = motherName;
                    this.isLoadingServices = true;
                    try {
                        let response = await fetch('/services/' + motherId);
                        let result = await response.json();
                        this.childrenServices = result.data.children || [];
                    } catch (error) { console.error(error); }
                    this.isLoadingServices = false;
                },

                async fetchHistory() {
                    try {
                        let response = await fetch(this.historyUrl);
                        let result = await response.json();
                        this.transactions = result.data || [];
                    } catch (error) { console.error(error); }
                    this.isLoadingHistory = false;
                },

                submitTransaction() {
                    if (this.activeForm === 'withdraw') {
                        this.executePostRequest('withdraw_static_nonce');
                    } else if (this.activeForm === 'deposit' && this.braintreeInstance) {
                        this.isTxProcessing = true;
                        this.braintreeInstance.requestPaymentMethod((err, payload) => {
                            if (err) { 
                                this.txError = 'Please select or fill payment method details.'; 
                                this.isTxProcessing = false; 
                                return; 
                            }
                            this.executePostRequest(payload.nonce);
                        });
                    }
                },

                async executePostRequest(nonceValue) {
                    this.isTxProcessing = true;
                    this.txError = '';

                    try {
                        let response = await fetch(this.storeUrl, {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'X-CSRF-TOKEN': this.csrfToken 
                            },
                            body: JSON.stringify({
                                amount: this.amount,
                                type: this.activeForm,
                                currency: 'USD',
                                nonce: nonceValue
                            })
                        });
                        let data = await response.json();

                        if (response.ok) {
                            this.activeForm = null;
                            this.fetchHistory();
                            alert(data.message || 'Transaction completed successfully!');
                            window.location.reload();
                        } else {
                            this.txError = data.message || 'Transaction failed.';
                        }
                    } catch (e) { this.txError = 'Server error.'; }
                    this.isTxProcessing = false;
                }
            };
        }
    </script>
</x-app-layout>