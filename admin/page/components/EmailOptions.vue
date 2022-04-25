<span id="emailOptions">
  <span v-if="shared.invoiceId" >
      <label for="email_images"><input id="email_images" v-model="emailOptions.email_images" type="checkbox"> Images</label>
      <label style="padding-left: 10px;" for="email_to"><input id="email_to" v-model="emailOptions.email_to" type="email" placeholder="Send email to"></label>
      <button :disabled="loading" @click="save">{{ loading ? 'Email Sending Please wait...' : 'Send Email' }}</button>
  </span>
</span>

<script>
window.wrapper = new Vue({
  el: '#emailOptions',
  data() {
    return {
      emailOptions: shared.emailOptions,
      loading: false,
    }
  },
  methods: {
    save() {
      this.loading = true
      const what = {
        id: shared.invoiceId,
        emailOptions: this.emailOptions
      }
      this.pushToEstimationService('updatePageData', what)
          .then(response => {
            let url = '/admin/invoice/index-email.php?invoice_id='+what.id+'&email=y&token=creedDefaultToken&title=ESTIMATION';
            axios.get(url).then(success => {
              alert('Email sent successfully');
            }).finally(opt => {
              this.loading = false;
            });
          })
          .catch(error => {
            this.loading = false;
          })
    }
  }
});
</script>

<style>
  #emailOptions {
    padding: 10px;
    border: 1px solid #f1f1f1;
  }
</style>