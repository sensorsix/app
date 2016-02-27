<div style="font:14px/1.4285714 Arial,sans-serif;color:#333">
  <div class="adM">
  </div>
  <table style="width:100%;border-collapse:collapse">
    <tbody>
    <tr>
      <td style="font:14px/1.4285714 Arial,sans-serif;padding:10px 10px 0;background:#f5f5f5">
        <table style="width:100%;border-collapse:collapse">
          <tbody>
          <tr>
            <td style="font:14px/1.4285714 Arial,sans-serif;padding:0">
              <div style="background:#fff;border:1px solid #ccc;border-radius:5px;padding:20px">
                <table style="width:100%;border-collapse:collapse">
                  <tbody>
                  <tr>
                    <td style="font:14px/1.4285714 Arial,sans-serif;padding:0">
                      <p style="margin-bottom:0">
                        Please <span class="il">confirm</span> your email by clicking the button below.
                      </p>
                    </td>
                  </tr>
                  <tr>
                    <td style="font:14px/1.4285714 Arial,sans-serif;padding:15px 0 0">
                      <table style="width:auto;border-collapse:collapse">
                        <tbody>
                        <tr>
                          <td style="font:14px/1.4285714 Arial,sans-serif;padding:0">
                            <div style="border:1px solid #486582;border-radius:3px;background:#3068a2">
                              <table style="width:auto;border-collapse:collapse">
                                <tbody>
                                <tr>
                                  <td style="font:14px/1.4285714 Arial,sans-serif;padding:4px 10px">
                                    <a target="_blank" style="color:white;text-decoration:none;font-weight:bold"
                                       href="<?php echo url_for('@sf_guard_register\confirmation?token=' . $user->salt, true) ?>"><span
                                        class="il">Confirm</span> this email address</a>
                                  </td>
                                </tr>
                                </tbody>
                              </table>
                            </div>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </td>
          </tr>
          </tbody>
        </table>
      </td>
    </tr>
    </tbody>
  </table>
</div>