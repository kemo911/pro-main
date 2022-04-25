<div id="modalForPreviewDotInfo">
    <input type="hidden" id="vueModalDotInfo" :value="JSON.stringify(dots)">
    <div id="dotPreviewModal" class="modal fade" role="dialog">
        <input type="hidden" ref="dotNumber" id="dotNumber" value="">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="dot_name">{{ shared.response.name ?? '' }}</h4>
                </div>

                <div class="modal-body">

                    <div id="dot_section_2">
                        <div class="row">
                            <div class="col-md-12" style="border: 1px solid #e4e4e4; padding: 10px;">
                                <div v-for="ledger in ledgers[0]" :key="ledger.key" class="col-md-3">
                                    <div class="btn-group">
                                      <h5>{{ ledger.name }}</h5>
                                      <ul style="list-style: none; margin: 0; padding: 8px 0;">
                                        <li><label class="btn btn-xs btn-success"><input type="radio" :checked="input.ledger.size === ledger.key && input.ledger.mm === 10" @click="setOpt(ledger.key, 10)" name="dot_input" value="10"> <10mm</label></li>
                                        <li><label class="btn btn-xs btn-success"><input type="radio" :checked="input.ledger.size === ledger.key && input.ledger.mm === 20" @click="setOpt(ledger.key, 20)" name="dot_input" value="20"> <20mm</label></li>
                                        <li><label class="btn btn-xs btn-success"><input type="radio" :checked="input.ledger.size === ledger.key && input.ledger.mm === 30" @click="setOpt(ledger.key, 30)" name="dot_input" value="30"> <30mm</label></li>
                                      </ul>
                                    </div>
                                </div>
                                <div v-if="[1,2,4].includes(shared.dotNumber)" v-for="ledger in ledgers[1]" :key="ledger.key" class="col-md-3">
                                    <div class="btn-group">
                                      <h5>{{ ledger.name }}</h5>
                                      <ul style="list-style: none; margin: 0; padding: 8px 0;">
                                        <li><label class="btn btn-xs btn-success"><input type="radio" :checked="input.ledger.size === ledger.key && input.ledger.mm === 10" @click="setOpt(ledger.key, 10)" name="dot_input" value="10"> <10mm</label></li>
                                        <li><label class="btn btn-xs btn-success"><input type="radio" :checked="input.ledger.size === ledger.key && input.ledger.mm === 20" @click="setOpt(ledger.key, 20)" name="dot_input" value="20"> <20mm</label></li>
                                        <li><label class="btn btn-xs btn-success"><input type="radio" :checked="input.ledger.size === ledger.key && input.ledger.mm === 30" @click="setOpt(ledger.key, 30)" name="dot_input" value="30"> <30mm</label></li>
                                      </ul>
                                    </div>
                                </div>
                                <button type="button" @click="clearOpt">Clear Selection</button>
                            </div>

                            <div class="col-md-12" style="margin-top: 20px; padding: 10px;">
                                <div class="col-md-6">
                                    <div class="btn-group">
                                      <label class="btn btn-xs btn-success"><input type="checkbox" v-model="input.DA"> DA</label>
                                      <label class="btn btn-xs btn-success"><input type="checkbox" v-model="input.PTP"> PTP</label>
                                      <label class="btn btn-xs btn-success"><input type="checkbox" v-model="input.RC"> RC</label>
                                      <label class="btn btn-xs btn-success"><input type="checkbox" v-model="input.AL"> AL</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                  <label>SD</label>
                                  <input type="number" v-model="input.SD" step="1" min="0" placeholder="SD">
                                </div>

                                <div v-if="[2, 6,7,11,12, 5,13].includes(shared.dotNumber)" class="col-md-12" style="margin: 10px 0;">
                                  <table class="table table-bordered">
                                    <thead>
                                      <tr>
                                        <th>Pièces</th>
                                        <th>Description</th>
                                        <th># Hrs</th>
                                        <th>Prix</th>
                                      </tr>
                                    </thead>
                                    <tbody v-if="[2].includes(shared.dotNumber)">
                                      <tr v-for="item in parts.groupA" :key="item.id">
                                        <td>
                                          <div class="btn-group">
                                            <label class="btn btn-success btn-xs">
                                              <input :checked="input.parts.includes(item)" class="part-checkbox" type="checkbox" :value="item.id" @click="togglePartsToInput(item, $event)" autocomplete="off"> {{ item.label }}
                                            </label>
                                          </div>
                                        </td>
                                        <td>
                                          <input type="text" disabled @change="generateNote" v-model="item.desc" :class="'part_desc_' + item.id" style="width: 300px; border: 1px solid #333; padding: 3px;">
                                        </td>
                                        <td>
                                          <input type="number" disabled v-model="item.hrs" :class="'part_hrs_' + item.id" style="width: 100px; border: 1px solid #333; padding: 3px;" >
                                        </td>
                                        <td>
                                          <input type="number" disabled v-model="item.price" :class="'part_price_' + item.id" style="width: 100px; border: 1px solid #333; padding: 3px;" >
                                        </td>
                                      </tr>
                                    </tbody>
                                    <tbody v-else-if="[6,7,11,12].includes(shared.dotNumber)">
                                      <tr v-for="item in parts.groupB" :key="item.id">
                                        <td>
                                          <div class="btn-group">
                                            <label class="btn btn-success btn-xs">
                                              <input :checked="input.parts.includes(item)" class="part-checkbox" type="checkbox" :value="item.id" @click="togglePartsToInput(item, $event)" autocomplete="off"> {{ item.label }}
                                            </label>
                                          </div>
                                        </td>
                                        <td v-if="![10,11].includes(item.id)">
                                          <input type="text" disabled @change="generateNote" v-model="item.desc" :class="'part_desc_' + item.id" style="width: 300px; border: 1px solid #333; padding: 3px;">
                                        </td>
                                        <td :colspan="[10,11].includes(item.id) ? 3 : 1">
                                          <input type="number" disabled v-model="item.hrs" :class="'part_hrs_' + item.id" style="width: 100px; border: 1px solid #333; padding: 3px;" >
                                        </td>
                                        <td v-if="![10,11].includes(item.id)">
                                          <input type="number" disabled v-model="item.price" :class="'part_price_' + item.id" style="width: 100px; border: 1px solid #333; padding: 3px;" >
                                        </td>
                                      </tr>
                                    </tbody>
                                    <tbody v-else-if="[5,13].includes(shared.dotNumber)">
                                      <tr v-for="item in parts.groupC" :key="item.id">
                                        <td>
                                          <div class="btn-group">
                                            <label class="btn btn-success btn-xs">
                                              <input :checked="input.parts.includes(item)" class="part-checkbox" type="checkbox" :value="item.id" @click="togglePartsToInput(item, $event)" autocomplete="off"> {{ item.label }}
                                            </label>
                                          </div>
                                        </td>
                                        <td>
                                          <input type="text" disabled @change="generateNote" v-model="item.desc" :class="'part_desc_' + item.id" style="width: 300px; border: 1px solid #333; padding: 3px;">
                                        </td>
                                        <td>
                                          <input type="number" disabled v-model="item.hrs" :class="'part_hrs_' + item.id" style="width: 100px; border: 1px solid #333; padding: 3px;" >
                                        </td>
                                        <td>
                                          <input type="number" disabled v-model="item.price" :class="'part_price_' + item.id" style="width: 100px; border: 1px solid #333; padding: 3px;" >
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>

                                <div class="hidden">
                                    <div class="form-group">
                                        <input type="text" placeholder="Pricing" readonly="readonly" class="form-control" required="required" name="dot[pricing]" value="0" id="dot_pricing">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>



                    <hr>


<!--                    <pre>-->
<!--                      {{ input }}-->
<!--                    </pre>-->

                    <hr>

                    <div id="dot_section_1">

                        <input type="hidden" id="dot_number" value="">

                        <div class="form-group">

                            <label for="dot_tech">Tech: </label>

                            <select name="dot[tech]" class="form-control" id="dot_tech">

                                <?php foreach ( $techGuys as $techGuy ): ?>

                                    <option value="<?php echo $techGuy['user_id']; ?>" <?php echo $currentUser['user_id'] == $techGuy['user_id'] ? 'selected' : '' ?>><?php echo $techGuy['name']; ?></option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="form-group">

                            <label for="dot_desc">Note: </label>

                            <input type="text" class="form-control" id="dot_desc" :value="input.notes.join(', ')">

                        </div>

                        <div class="form-group clearfix">

                            <div v-show="[2, 6,7,11,12, 5,13].includes(shared.dotNumber)" class="btn-dot-group" style="width: 30%; margin-right: 5%; float: left;">
                                <label for="part_dot_price" style="color: #e74c3c;">Prix des pièces: </label>
                                <input type="number" readonly class="form-control" id="part_dot_price" :value="partPrice">
                            </div>

                            <div v-show="[6,7,11,12, 5,13].includes(shared.dotNumber)" id="damagework_force_price_container" style="width: 30%; float: left;">
                                <label for="damagework_force_price">Prix main d’oeuvre: </label>
                                <input type="number"  class="form-control" v-if="[6,7,11,12].includes(shared.dotNumber)" id="damagework_force_price" :value="damageWorkPrice">
                                <input type="number"  class="form-control" v-else id="backwindow_price" :value="backWindowPrice">
                            </div>

                            <div style="width: 30%; float: right;">
                                <label for="dot_price">Prix: </label>
                                <input type="number"  class="form-control" id="dot_price" :value="dotPrice">
                            </div>

                        </div>

                    </div>



                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" ng-click="addDotInfo()" @click="addDotInfo" id="addDotInfo" type="button">Ajouter</button>
                    <button class="btn btn-warning" data-dismiss="modal" data-target="#dotPreviewModal">Fermer</button>
                </div>

            </div>



        </div>

    </div>
</div>

<style>
  #dotPreviewModal ul li label {
    font-weight: 500;
  }
  #dotPreviewModal h5 {
    font-weight: 700;
  }
</style>