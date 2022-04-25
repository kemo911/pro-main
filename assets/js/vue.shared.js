window.shared = Vue.observable({
    isMounted: false,
    dotNumber: null,
    response: {
        name: '',
        10: {},
        20: {},
        30: {},
    },
    ready: false,
    dots: {
        1: null,
        2: null,
        3: null,
        4: null,
        5: null,
        6: null,
        7: null,
        8: null,
        9: null,
        10: null,
        11: null,
        12: null,
        13: null,
        14: null,
        15: null,
    },
    backGlass: {
        enabled: false,
        hour: null
    },
    windShield: {
        id: 20,
        enabled: false,
        desc: '',
        price: null,
        hour: null,
    },
    emailOptions: {
        email_images: false,
        email_to: '',
    },
    invoiceId: null,
});

const GeneratePriceFromDots = {
    install(Vue, options) {
        Vue.prototype.generateAllPricesFromDots = (shared) => {
            let dots = shared.dots;
            let damagePrice = 0;
            let partPrice = 0;
            let backWindowPrice = 0;
            for (const [i, dot] of Object.entries(dots)) {
                if (dot) {
                    if ([6,7,11,12].includes( parseInt(i) )) {
                        if (dot.prices.damageWorkPrice) {
                            damagePrice += parseFloat(dot.prices.damageWorkPrice)
                        }
                        if (dot.prices.partPrice) {
                            partPrice += parseFloat(dot.prices.partPrice)
                        }
                    }
                    if ([5,13].includes( parseInt(i) )) {
                        backWindowPrice += parseFloat(dot.prices.backWindowPrice)
                    }
                }
            }

            if (shared.windShield.enabled) {
                partPrice += parseFloat(shared.windShield.price)
            }

            if (shared.backGlass.enabled) {
                backWindowPrice += parseFloat(shared.backGlass.hour) * 65 + 64
            }

            return {
                damagePrice, partPrice, backWindowPrice
            };
        }
    },
}

const PushToEstimationService = {
    install(Vue, options) {
        Vue.prototype.pushToEstimationService = async (where, what) => {
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            };
            const response = await axios.post('/admin/ajax/ajax_estimations.php', {
                action: where,
                data: what
            }, headers);

            return response.data;
        }
    }
}

Vue.use(GeneratePriceFromDots);
Vue.use(PushToEstimationService);
