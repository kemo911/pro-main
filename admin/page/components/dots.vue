<script>
  window.DotModal = new Vue({
    el: '#modalForPreviewDotInfo',
    data() {
      return {
        dots: shared.dots,
        input: {
          ledger: {
            size: null,
            mm: null,
            price: 0,
          },
          DA: false,
          PTP: false,
          RC: false,
          AL: false,
          SD: null,
          notes: [],
          parts: [],
          prices: {
            dotPrice: 0,
            damageWorkPrice: 0,
            backWindowPrice: 0,
            partPrice: 0,
          },
        },
        ledgers: [
          [
            {
              name: 'TRÈS LÉGER 1-5X',
              key: 5
            },
            {
              name: 'LÉGER 6-15X',
              key: 15
            },
            {
              name: 'MODÉRER 16-30X',
              key: 30
            },
            {
              name: 'MÉDIUM 31-50X',
              key: 50
            },
          ],
          [
            {
              name: 'LOURD 51-75X',
              key: 75
            },
            {
              name: 'SÉVÈRE 76-100X',
              key: 100
            },
            {
              name: 'EXTRÊME 101-150X',
              key: 150
            },
            {
              name: 'LIMITE 151-200X',
              key: 200
            },
          ],
        ],
        parts: {
          groupA: [
            {
              id: 0,
              label: "MLR TOIT G.",
              desc: '',
              hrs: 0,
              price: 0,
            },
            {
              id: 1,
              label: "MLR TOIT D.",
              desc: '',
              hrs: 0,
              price: 0,
            }
          ],
          groupB: [
            {
              id: 2,
              label: "MLR Sitière",
              desc: '',
              hrs: 0,
              price: 0,
            },
            {
              id: 3,
              label: "Lêche vitre",
              desc: '',
              hrs: 0,
              price: 0,
            },
            {
              id: 4,
              label: "Appliqué",
              desc: '',
              hrs: 0,
              price: 0,
            },
            {
              id: 10,
              label: "E/R",
              desc: '',
              hrs: 0,
              price: 0,
            },
            {
              id: 11,
              label: "E/R Compact",
              desc: '',
              hrs: 0,
              price: 0,
            }
          ],
          groupC: [
            {
              id: 5,
              label: "MLR VITRE",
              desc: '',
              hrs: 0,
              price: 0,
            },
          ],
        },
      }
    },
    computed: {
      dotPrice: function () {
        let price = 0;
        price += this.input.ledger.price ? parseFloat(this.input.ledger.price) : 0
        if (this.input.SD) {
          price += parseFloat(parseInt(this.input.SD) * 50)
        }
        if (price && this.input.AL) {
          price += parseFloat(price * 0.25)
        }
        this.input.prices.dotPrice = price;
        return price ? price.toFixed(2) : ''
      },
      damageWorkPrice: function () {
        let price = 0;

        for (const [i, part] of this.input.parts.entries()) {
          if (part.hrs && part.id !== 5) {
            price += parseFloat(part.hrs) * 65
          }
        }
        this.input.prices.damageWorkPrice = price;

        return price ? price.toFixed(2) : ''
      },
      backWindowPrice: function () {
        let backWindowPrice = 0;
        for (const [i, part] of this.input.parts.entries()) {
          if (part.hrs) {
            if (part.id === 5) {
              backWindowPrice += parseFloat(part.hrs) * 65
            }
          }
        }
        this.input.prices.backWindowPrice = backWindowPrice;

        return backWindowPrice ? backWindowPrice.toFixed(2) : ''
      },
      partPrice: function () {
        let price = 0;
        for (const [i, part] of this.input.parts.entries()) {
          if (part.price) {
            price += parseFloat(part.price)
          }
        }
        this.input.prices.partPrice = price;
        return price ? price.toFixed(2) : ''
      },
    },
    watch: {
      'input.DA': function (value) {
        this.generateNote();
      },
      'input.PTP': function (value) {
        this.generateNote();
      },
      'input.RC': function (value) {
        this.generateNote();
      },
      'input.AL': function (value) {
        this.generateNote();
      },
      'input.SD': function (value) {
        this.generateNote();
      },
    },
    methods: {
      setOpt(size, mm) {
        this.input.ledger.size = size;
        this.input.ledger.mm = mm;
        if (shared.response[mm]) {
          this.input.ledger.price = shared.response[mm][size];
        }
        this.generateNote();
      },
      clearOpt() {
        this.input.ledger.size = null;
        this.input.ledger.mm = null;
        this.input.ledger.price = 0
        this.generateNote();
      },
      toggleVal(nest) {
        this.input[nest] = !this.input[nest];
      },
      togglePartsToInput(item, event) {
        if (jQuery(event.target).is(':checked')) {
          jQuery('.part_desc_' + event.target.value).attr('disabled', false);
          jQuery('.part_hrs_' + event.target.value).attr('disabled', false);
          jQuery('.part_price_' + event.target.value).attr('disabled', false);
          this.input.parts.push(item);
        } else {
          jQuery('.part_desc_' + event.target.value).attr('disabled', true);
          jQuery('.part_hrs_' + event.target.value).attr('disabled', true);
          jQuery('.part_price_' + event.target.value).attr('disabled', true);
          this.input.parts.splice(this.input.parts.indexOf(item), 1);
        }
        this.generateNote()
      },
      addDotInfo() {
        if (shared.dotNumber) {
          this.dots[shared.dotNumber] = JSON.parse(JSON.stringify(this.input));
        }
        const {damagePrice, backWindowPrice, partPrice} = this.generateAllPricesFromDots(shared);
        jQuery('#inv_work_force_price').val(damagePrice.toFixed(2))
        jQuery('#inv_parts_price').val(partPrice.toFixed(2))
        jQuery('#inv_glazier_price').val(backWindowPrice.toFixed(2))
        this.fillBackground(shared.dotNumber)
      },
      fillBackground(number) {
        jQuery('.dot-' + number).css({
          "background-color": "#ed1c24"
        });
      },
      generateNote() {
        this.input.notes = [];

        for (const [i, part] of this.input.parts.entries()) {
          this.input.notes.push(part.label + '(' + part.desc + ')');
        }

        if (this.input.ledger.size && this.input.ledger.mm) {
          this.input.notes.push(
              this.getSizeLabel(this.input.ledger.size) + ' ' + this.input.ledger.mm + 'mm'
          );
        }

        if (this.input.DA) {
          this.input.notes.push('DA');
        }
        if (this.input.PTP) {
          this.input.notes.push('PTP');
        }
        if (this.input.RC) {
          this.input.notes.push('RC');
        }
        if (this.input.AL) {
          this.input.notes.push('AL');
        }
        if (this.input.SD) {
          this.input.notes.push('SD (' + this.input.SD + 'x)');
        }
      },
      getSizeLabel(size) {
        switch (size) {
          case 5:
            return '1-5x';
          case 15:
            return '6-15x';
          case 30:
            return '16-30x';
          case 50:
            return '31-50x';
          case 75:
            return '51-75x';
          case 100:
            return '76-100x';
          case 150:
            return '101-150x';
          case 200:
            return '150-200x';
        }
      },
      getCurrentKey() {
        if ([2].includes(shared.dotNumber)) {
          return 'groupA';
        } else if ([5,13].includes(shared.dotNumber)) {
          return 'groupC';
        } else if ([6,7,11,12].includes(shared.dotNumber)) {
          return 'groupB';
        }

        return null
      },
      fakeMounted() {
        if (this.dots[shared.dotNumber]) {
          this.input = JSON.parse(JSON.stringify(this.dots[shared.dotNumber]));
          const key = this.getCurrentKey();
          if (this.parts[key]) {
            for (const [i, part] of this.parts[key].entries()) {
              for (const [j, item] of this.input.parts.entries()) {
                if (item.id === part.id) {
                  this.parts[key][i] = item;
                }
              }
            }
          }
        }
        this.getPrices();
      },
      fakeUnmounted() {
        const selector = jQuery('input[name="dot_input"]');
        selector.attr('checked', false);
        selector.prop('checked', false);
        jQuery('.part-checkbox').attr('checked', false).prop('checked', false);
        this.input = {
          ledger: {
            size: null,
            mm: null,
            price: 0,
          },
          DA: false,
          PTP: false,
          RC: false,
          AL: false,
          SD: null,
          notes: [],
          parts: [],
          prices: {
            dotPrice: 0,
            damageWorkPrice: 0,
            backWindowPrice: 0,
            partPrice: 0,
          },
        };
        shared.response = {
          name: '',
          10: {},
          20: {},
          30: {},
        };
        this.parts = {
          groupA: [
            {
              id: 0,
              label: "MLR TOIT G.",
              desc: '',
              hrs: 0,
              price: 0,
            },
            {
              id: 1,
              label: "MLR TOIT D.",
              desc: '',
              hrs: 0,
              price: 0,
            }
          ],
          groupB: [
            {
              id: 2,
              label: "MLR Sitière",
              desc: '',
              hrs: 0,
              price: 0,
            },
            {
              id: 3,
              label: "Lêche vitre",
              desc: '',
              hrs: 0,
              price: 0,
            },
            {
              id: 4,
              label: "Appliqué",
              desc: '',
              hrs: 0,
              price: 0,
            },
            {
              id: 10,
              label: "E/R",
              desc: '',
              hrs: 0,
              price: 0,
            },
            {
              id: 11,
              label: "E/R Compact",
              desc: '',
              hrs: 0,
              price: 0,
            }
          ],
          groupC: [
            {
              id: 5,
              label: "MLR VITRE",
              desc: '',
              hrs: 0,
              price: 0,
            },
          ],
        };
      },
      init() {
        for (const [i, dot] of Object.entries(this.dots)) {
          if (dot) {
            this.fillBackground(parseInt(i))
          }
        }
      },
      getPrices() {
        fetch('/admin/ajax/ajax_get_pricing.php?type=car&number='+shared.dotNumber)
        .then(response => response.json())
        .then(data => shared.response = data)
      },
    },
    created() {
      this.$watch(() => shared.isMounted, (isMounted) => {
        if (isMounted) {
          console.log('Mounted');
          this.fakeMounted();
        } else {
          console.log('Unmounted');
          this.fakeUnmounted();
        }
      })
    },
    mounted() {
      const interval = setInterval(() => {
        console.log('IsReady: ', shared.ready);
        if (shared.ready) {
          const dots = document.getElementById('dots').value;
          if (dots) {
            this.dots = JSON.parse(dots);
            shared.dots = this.dots;
            this.init()
          }
          clearInterval(interval);
        }
      }, 1000)
    },
  });
</script>
