<div id="glazierSection" class="panel panel-primary">
<div class="panel-heading" role="tab" id="glazierHead">
  <h4 class="panel-title">
    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:void(0);" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
      Vitrier
    </a>
  </h4>
</div>
<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
  <div class="panel-body">
    <table class="table table-bordered">
      <tr>
        <th>#</th>
        <th>Pièce</th>
        <th>Prix</th>
        <th>Hrs</th>
      </tr>
      <tr>
        <td colspan="3"><label class="btn btn-success btn-xs"><input type="checkbox" v-model="backGlass.enabled"> E/R <sub>Backglass</sub></label></td>
        <td><input v-model="backGlass.hour" :disabled="!backGlass.enabled" min="0" @change="backGlassCalculation" type="number"></td>
      </tr>
      <tr>
        <td>
          <label class="btn btn-success btn-xs"><input type="checkbox" v-model="windShield.enabled"> Pare brise</label>
        </td>
        <td><input v-model="windShield.desc" :disabled="!windShield.enabled" type="text"></td>
        <td><input v-model="windShield.price" @change="windShieldCalculation" :disabled="!windShield.enabled" min="0" type="number"></td>
        <td><input v-model="windShield.hour" :disabled="!windShield.enabled" min="0" type="number"></td>
      </tr>
    </table>
  </div>
</div>
</div>

<script>
window.wrapper = new Vue({
  el: '#glazierSection',
  data() {
   return {
     backGlass: shared.backGlass,
     windShield: shared.windShield,
   }
  },
  watch: {
    'backGlass.enabled': function () {
      this.backGlassCalculation();
    },
    'backGlass.hour': function () {
      this.backGlassCalculation();
    },
    'windShield.enabled': function () {
      this.windShieldCalculation();
    },
    'windShield.hour': function () {
      this.windShieldCalculation();
    },
    'windShield.price': function () {
      this.windShieldCalculation();
    },
    'windShield.desc': function () {
      this.windShieldCalculation();
    },
  },
  mounted() {
    const interval = setInterval(() => {
      console.log('IsReady on BackWindow Component: ', shared.ready);
      if (shared.ready) {
        try {
          const sharedItems = document.getElementById('shared').value;
          if (sharedItems) {
            const sharedItemsObject = JSON.parse(sharedItems);
            this.windShield = sharedItemsObject.windShield;
            this.backGlass = sharedItemsObject.backGlass;
            shared.windShield = this.windShield;
            shared.backGlass = this.backGlass;
            setTimeout( () => {
              const {damagePrice, backWindowPrice, partPrice} = this.generateAllPricesFromDots(shared)
              jQuery('#inv_work_force_price').val(damagePrice.toFixed(2))
              jQuery('#inv_parts_price').val(partPrice.toFixed(2))
              jQuery('#inv_glazier_price').val(backWindowPrice.toFixed(2))
            }, 200);
          }
        } catch (e) {}
        clearInterval(interval);
      }
    }, 1000)
  },
  methods: {
    backGlassCalculation() {
      let {backWindowPrice} = this.generateAllPricesFromDots(shared);
      // if (this.backGlass.enabled && this.backGlass.hour) {
      //   backWindowPrice += this.backGlass.hour * 65 + 64
      // }
      jQuery('#inv_glazier_price').val(backWindowPrice.toFixed(2));
      if (this.backGlass.enabled) {
        this.addNote(' E/R BackGlass | ');
      } else {
        this.removeNote(' E/R BackGlass | ');
      }
    },
    windShieldCalculation() {
      const {damagePrice, backWindowPrice, partPrice} = this.generateAllPricesFromDots(shared);
      jQuery('#inv_work_force_price').val(damagePrice.toFixed(2))
      jQuery('#inv_parts_price').val(partPrice.toFixed(2))
      jQuery('#inv_glazier_price').val(backWindowPrice.toFixed(2))
      if (this.windShield.enabled) {
        this.addNote(' Uréthane et temps | ');
      } else {
        this.removeNote(' Uréthane et temps | ');
      }
    },
    addNote(text) {
      const note = jQuery('#inv_glazier_note');
      let currentNote = note.val();
      if (currentNote.includes(text) === false) {
        note.val(currentNote + text);
      }
    },
    removeNote(text) {
      const note = jQuery('#inv_glazier_note');
      let currentNote = note.val();
      note.val(currentNote.replace(text, ''));
    }
  }
});
</script>

<style>
#glazierSection input {
  width: 50px;
}
</style>