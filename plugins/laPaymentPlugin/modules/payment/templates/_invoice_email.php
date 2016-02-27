  <h2 class="h"><?php echo __('Invoice') ?></h2>

  <div class="invoice-logo"></div>

  <?php include_partial('Payment/select_bank', array('booking_type' => $payment->getBookingType())) ?>
  <table class="top-table">
    <tr>
      <th></th>
      <th class="color"><?php echo __('ARVE ESITAJA') ?>:</th>
    </tr>
    <tr>
      <td>
        <span class="bold"><?php echo __('ARVE SAAJA') ?></span>
        <?php echo $payment->getCustomerName() ?><br/>
        <?php echo $payment->getRawValue()->getCustomerAddress() ?>, <?php echo __('Eesti') ?><br/>
        <br/>
        <span class="bold"><?php echo __('ARVE nr.') ?><?php echo str_pad($payment->id, 10, '0', STR_PAD_LEFT) ?></span>
        <?php echo __('Viitenumber:') ?> <?php echo $payment->refnumber ?><br/>
        <?php echo __('Arve kuupäev:') ?> <?php echo myFormatDate($payment->created_at) ?><br/>
      </td>
      <td class="b-right">
        Lõuna Kindlustusmaakler OÜ<br/>
        Raekoja plats 20 51004 Tartu<br/>
        Äriregistrikood: 10763199<br/>
        Käibemaksukood: EE101027432<br/><br/>
        Arveldusarved:<br/>
        <table>
          <tr>
            <td>SEB pank</td>
            <td>10220115685012</td>
          </tr>
          <tr>
            <td>SWEDBANK</td>
            <td>221032850366</td>
          </tr>
          <tr>
            <td>DANSKE pank&nbsp;</td>
            <td>334426360003</td>
          </tr>
        </table>

      </td>
    </tr>
    <tr>
      <td class="bold" colspan="2"><?php echo __('Hea Klient!')?></td>
    </tr>
  </table>

  <table class="table">
    <tr>
      <th><?php echo __('Selgitus') ?></th>
      <th class="summa"><?php echo __('Summa') ?></th>
    </tr>
    <tr>
      <th><?php echo __('Kokku tasuda:') ?></th>
      <th><?php echo number_format($payment->getTotalPrice(), 2, '.', ''), ' ', __('krooni') ?></th>
    </tr>
  </table>

