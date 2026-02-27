<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora Real | PrismaHUB</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="//unpkg.com/alpinejs" defer></script> 

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: '#0F172A', 
                        card: '#1E293B', 
                        primary: '#3483FA', 
                        meli: '#FFE600',
                        success: '#00A650',
                        danger: '#EF4444'
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        /* Remove setas dos inputs number */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0F172A; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
    </style>
</head>
<body class="bg-dark text-slate-300 font-sans h-screen flex overflow-hidden" x-data="pricingCalculator()">

    @include('components.sidebar')

    <main class="flex-1 overflow-y-auto p-4 md:p-8 custom-scrollbar">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fa-solid fa-calculator text-meli"></i> Calculadora de Precificação
            </h1>
            <p class="text-slate-400 mt-2">Simule o preço de venda ideal considerando taxas do Mercado Livre e impostos.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-7 space-y-6">
                
                <div class="bg-card border border-slate-700 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-white font-bold mb-4 flex items-center gap-2 border-b border-slate-700 pb-2">
                        <i class="fa-solid fa-box text-primary"></i> Custos do Produto
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase block mb-1">Custo de Aquisição (R$)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-500">R$</span>
                                <input type="number" x-model="cost" step="0.01" class="w-full bg-dark border border-slate-600 rounded-lg py-3 pl-10 pr-4 text-white font-bold focus:border-meli outline-none transition focus:ring-1 focus:ring-meli">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase block mb-1">Custo Operacional (R$)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-500">R$</span>
                                <input type="number" x-model="operational" step="0.01" class="w-full bg-dark border border-slate-600 rounded-lg py-3 pl-10 pr-4 text-white font-bold focus:border-meli outline-none transition focus:ring-1 focus:ring-meli" placeholder="Embalagem, fita...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-card border border-slate-700 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-white font-bold mb-4 flex items-center gap-2 border-b border-slate-700 pb-2">
                        <i class="fa-solid fa-percent text-yellow-500"></i> Taxas de Venda
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase block mb-1">Comissão ML (%)</label>
                            <div class="relative">
                                <input type="number" x-model="fee_percent" class="w-full bg-dark border border-slate-600 rounded-lg py-3 pl-4 pr-8 text-white font-bold focus:border-meli outline-none transition focus:ring-1 focus:ring-meli">
                                <span class="absolute right-3 top-3 text-slate-500">%</span>
                            </div>
                            <div class="flex gap-2 mt-2">
                                <button @click="fee_percent = 11" class="text-[10px] bg-slate-800 hover:bg-slate-700 text-slate-300 px-2 py-1 rounded transition border border-slate-700">Clássico (11%)</button>
                                <button @click="fee_percent = 16" class="text-[10px] bg-slate-800 hover:bg-slate-700 text-slate-300 px-2 py-1 rounded transition border border-slate-700">Premium (16%)</button>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase block mb-1">Imposto Fiscal (%)</label>
                            <div class="relative">
                                <input type="number" x-model="tax_percent" class="w-full bg-dark border border-slate-600 rounded-lg py-3 pl-4 pr-8 text-white font-bold focus:border-meli outline-none transition focus:ring-1 focus:ring-meli">
                                <span class="absolute right-3 top-3 text-slate-500">%</span>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase block mb-1">Custo Fixo ML (R$)</label>
                             <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-500">R$</span>
                                <input type="number" x-model="fixed_fee" class="w-full bg-dark border border-slate-600 rounded-lg py-3 pl-10 pr-4 text-white font-bold focus:border-meli outline-none transition focus:ring-1 focus:ring-meli" placeholder="Ex: 6.00">
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">Para produtos < R$ 79</p>
                        </div>
                         <div class="md:col-span-3">
                            <label class="text-xs font-bold text-slate-500 uppercase block mb-1">Frete Grátis (R$)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-500">R$</span>
                                <input type="number" x-model="shipping_cost" class="w-full bg-dark border border-slate-600 rounded-lg py-3 pl-10 pr-4 text-white font-bold focus:border-meli outline-none transition focus:ring-1 focus:ring-meli">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-card border border-slate-700 rounded-2xl p-6 shadow-lg border-l-4 border-l-emerald-500">
                    <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-bullseye text-emerald-500"></i> Definição de Lucro
                    </h3>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase block mb-1">Margem Líquida Desejada (%)</label>
                        <div class="flex items-center gap-4">
                            <input type="range" x-model="margin_percent" min="0" max="100" class="w-full h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-emerald-500">
                            <div class="relative w-32">
                                <input type="number" x-model="margin_percent" class="w-full bg-dark border border-slate-600 rounded-lg py-3 pl-4 pr-8 text-white font-bold focus:border-emerald-500 outline-none transition focus:ring-1 focus:ring-emerald-500">
                                <span class="absolute right-3 top-3 text-slate-500">%</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-5">
                <div class="sticky top-6 space-y-6">
                    
                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-600 rounded-2xl p-8 text-center shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-meli opacity-5 rounded-bl-full -mr-10 -mt-10"></div>
                        
                        <p class="text-slate-400 uppercase text-xs font-bold tracking-widest mb-2">Preço de Venda Sugerido</p>
                        <div class="text-5xl font-extrabold text-white mb-2 tracking-tight">
                            R$ <span x-text="formatMoney(calculatePrice())"></span>
                        </div>
                        <p class="text-sm text-slate-400" x-show="calculatePrice() > 0">
                            Para lucrar <span class="text-emerald-400 font-bold">R$ <span x-text="formatMoney(calculateProfitValue())"></span></span> por venda.
                        </p>
                    </div>

                    <div class="bg-card border border-slate-700 rounded-2xl overflow-hidden shadow-lg">
                        <div class="p-4 bg-slate-800/50 border-b border-slate-700 font-bold text-white flex justify-between items-center">
                            <span>Demonstrativo Financeiro (DRE)</span>
                            <i class="fa-solid fa-list-ol text-slate-500"></i>
                        </div>
                        <div class="p-6 space-y-3 text-sm">
                            <div class="flex justify-between text-white font-bold text-lg pb-2 border-b border-slate-700">
                                <span>Receita Bruta</span>
                                <span>R$ <span x-text="formatMoney(calculatePrice())"></span></span>
                            </div>
                            
                            <div class="flex justify-between text-red-400">
                                <span>(-) Custo Produto + Op.</span>
                                <span>- R$ <span x-text="formatMoney(parseFloat(cost || 0) + parseFloat(operational || 0))"></span></span>
                            </div>

                            <div class="flex justify-between text-red-400">
                                <span>(-) Comissão ML (<span x-text="fee_percent"></span>%)</span>
                                <span>- R$ <span x-text="formatMoney(calculatePrice() * (fee_percent/100))"></span></span>
                            </div>

                            <div class="flex justify-between text-red-400" x-show="fixed_fee > 0 && calculatePrice() < 79">
                                <span>(-) Taxa Fixa ML</span>
                                <span>- R$ <span x-text="formatMoney(fixed_fee)"></span></span>
                            </div>

                            <div class="flex justify-between text-red-400">
                                <span>(-) Impostos (<span x-text="tax_percent"></span>%)</span>
                                <span>- R$ <span x-text="formatMoney(calculatePrice() * (tax_percent/100))"></span></span>
                            </div>

                            <div class="flex justify-between text-red-400" x-show="shipping_cost > 0">
                                <span>(-) Frete</span>
                                <span>- R$ <span x-text="formatMoney(shipping_cost)"></span></span>
                            </div>

                            <div class="flex justify-between text-emerald-400 font-bold text-lg pt-3 border-t border-slate-700 mt-2">
                                <span>(=) Lucro Líquido</span>
                                <span>R$ <span x-text="formatMoney(calculateProfitValue())"></span></span>
                            </div>
                            
                             <div class="flex justify-between text-slate-500 text-xs mt-1">
                                <span>Margem Real</span>
                                <span><span x-text="calculateRealMargin()"></span>%</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-500/10 border border-blue-500/30 p-4 rounded-xl flex gap-3 items-start animate-fade-in" x-show="calculatePrice() < 79 && shipping_cost > 0">
                        <i class="fa-solid fa-circle-info text-blue-400 mt-1"></i>
                        <div class="text-sm text-blue-300">
                            <strong>Atenção:</strong> Seu preço sugerido está abaixo de R$ 79,00. O Mercado Livre geralmente não cobra frete grátis do vendedor nessa faixa, mas aplica uma taxa fixa por venda. Verifique se o custo de frete inserido é realmente necessário.
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <script>
        function pricingCalculator() {
            return {
                cost: '',
                operational: 0,
                fee_percent: 11,
                tax_percent: 4, // Simples Nacional médio
                fixed_fee: 6.00, // Taxa fixa padrão ML para < 79
                shipping_cost: 0,
                margin_percent: 20,

                calculatePrice() {
                    let cost = parseFloat(this.cost) || 0;
                    let operational = parseFloat(this.operational) || 0;
                    let shipping = parseFloat(this.shipping_cost) || 0;
                    let fixedFee = parseFloat(this.fixed_fee) || 0;
                    
                    // Se o preço ficar abaixo de 79, adiciona a taxa fixa.
                    // Isso gera uma circularidade simples, então vamos estimar primeiro.
                    
                    let fixedCosts = cost + operational + shipping;
                    
                    // Soma das porcentagens que incidem sobre a venda (Comissão + Imposto + Margem)
                    let totalDeductionsPercent = (parseFloat(this.fee_percent) + parseFloat(this.tax_percent) + parseFloat(this.margin_percent)) / 100;

                    // Evita divisão por zero ou negativa
                    if (totalDeductionsPercent >= 1) return 0;

                    // Fórmula de Mark-up divisor: Preço = Custos Fixos / (1 - Deduções%)
                    let price = fixedCosts / (1 - totalDeductionsPercent);

                    // Lógica da Taxa Fixa (< 79 reais)
                    if (price < 79) {
                         // Recalcula incluindo a taxa fixa nos custos fixos
                         price = (fixedCosts + fixedFee) / (1 - totalDeductionsPercent);
                    }
                    
                    return price > 0 ? price : 0;
                },

                calculateProfitValue() {
                    let price = this.calculatePrice();
                    if(price === 0) return 0;
                    
                    let cost = parseFloat(this.cost) || 0;
                    let operational = parseFloat(this.operational) || 0;
                    let shipping = parseFloat(this.shipping_cost) || 0;
                    let fixedFee = (price < 79) ? (parseFloat(this.fixed_fee) || 0) : 0;
                    
                    let deductions = price * ((parseFloat(this.fee_percent) + parseFloat(this.tax_percent)) / 100);
                    
                    return price - cost - operational - shipping - fixedFee - deductions;
                },
                
                calculateRealMargin() {
                    let price = this.calculatePrice();
                    if(price === 0) return 0;
                    let profit = this.calculateProfitValue();
                    return ((profit / price) * 100).toFixed(2);
                },

                formatMoney(value) {
                    return value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
</body>
</html>