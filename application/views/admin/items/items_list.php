<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('39', 'created');
$edited = can_action('39', 'edited');
$deleted = can_action('39', 'deleted');
if (!empty($created) || !empty($edited)){
?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('all_items') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                            data-toggle="tab"><?= lang('new_items') ?></a></li>

        <li class="<?= $active == 3 ? 'active' : ''; ?>"><a href="#group"
                                                            data-toggle="tab"><?= lang('group') . ' ' . lang('list') ?></a>
        </li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
            <div class="panel panel-custom">
                <header class="panel-heading ">
                    <div class="panel-title"><strong><?= lang('all_items') ?></strong></div>
                </header>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?= lang('item') ?></th>
                            <?php
                            $invoice_view = config_item('invoice_view');
                            if (!empty($invoice_view) && $invoice_view == '2') {
                                ?>
                                <th><?= lang('hsn_code') ?></th>
                            <?php } ?>
                            <th class="col-sm-1"><?= lang('qty') ?></th>
                            <th class="col-sm-1"><?= lang('unit_price') ?></th>
                            <th class="col-sm-1"><?= lang('unit') . ' ' . lang('type') ?></th>
                            <th class="col-sm-2"><?= lang('tax') ?></th>
                            <th class="col-sm-1"><?= lang('group') ?></th>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <th class="col-sm-1"><?= lang('action') ?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $all_items = $this->db->get('tbl_saved_items')->result();
                        $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                        $total_balance = 0;
                        foreach ($all_items as $v_items):
                            $group = $this->db->where('customer_group_id', $v_items->customer_group_id)->get('tbl_customer_group')->row();
                            $item_name = $v_items->item_name ? $v_items->item_name : $v_items->item_desc;

                            ?>
                            <tr id="table_items_<?= $v_items->saved_items_id ?>">
                                <td><strong class="block"><?= $item_name ?></strong>
                                    <?= nl2br($v_items->item_desc) ?>
                                </td>
                                <?php
                                $invoice_view = config_item('invoice_view');
                                if (!empty($invoice_view) && $invoice_view == '2') {
                                    ?>
                                    <td><?= $v_items->hsn_code ?></td>
                                <?php } ?>
                                <td><?= $v_items->quantity ?></td>
                                <td><?= display_money($v_items->unit_cost, $currency->symbol); ?></td>
                                <td><?= $v_items->unit_type; ?></td>
                                <td>
                                    <?php
                                    if (!is_numeric($v_items->tax_rates_id)) {
                                        $tax_rates = json_decode($v_items->tax_rates_id);
                                    } else {
                                        $tax_rates = null;
                                    }
                                    if (!empty($tax_rates)) {
                                        foreach ($tax_rates as $key => $tax_id) {
                                            $taxes_info = $this->db->where('tax_rates_id', $tax_id)->get('tbl_tax_rates')->row();
                                            if (!empty($taxes_info)) {
                                                echo $key + 1 . '. ' . $taxes_info->tax_rate_name . '&nbsp;&nbsp; (' . $taxes_info->tax_rate_percent . '% ) <br>';
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?= (!empty($group->customer_group) ? $group->customer_group : ' '); ?></td>
                                <?php if (!empty($edited) || !empty($deleted)) { ?>
                                    <td>
                                        <?php if (!empty($edited)) { ?>
                                            <?= btn_edit('admin/items/items_list/' . $v_items->saved_items_id) ?>
                                        <?php }
                                        if (!empty($deleted)) { ?>
                                            <?php echo ajax_anchor(base_url("admin/items/delete_items/" . $v_items->saved_items_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_items_" . $v_items->saved_items_id)); ?>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                            </tr>
                            <?php
                        endforeach;
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($created) || !empty($edited)){ ?>
            <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data" id="form"
                      action="<?php echo base_url(); ?>admin/items/saved_items/<?php
                      if (!empty($items_info)) {
                          echo $items_info->saved_items_id;
                      }
                      ?>" method="post" class="form-horizontal  ">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('item_name') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" value="<?php
                            if (!empty($items_info)) {
                                echo $items_info->item_name;
                            }
                            ?>" name="item_name" required="">
                        </div>

                    </div>
                    <!-- End discount Fields -->
                    <div class="form-group terms">
                        <label class="col-lg-3 control-label"><?= lang('description') ?> </label>
                        <div class="col-lg-5">
                        <textarea name="item_desc" class="form-control"><?php
                            if (!empty($items_info)) {
                                echo $items_info->item_desc;
                            }
                            ?></textarea>
                        </div>
                    </div>
                    <?php
                    $invoice_view = config_item('invoice_view');
                    if (!empty($invoice_view) && $invoice_view == '2') {
                        ?>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('hsn_code') ?></label>
                            <div class="col-lg-5">
                                <input type="text" data-parsley-type="number" class="form-control" value="<?php
                                if (!empty($items_info)) {
                                    echo $items_info->hsn_code;
                                }
                                ?>" name="hsn_code" required="">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('unit_price') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <input type="text" data-parsley-type="number" class="form-control" value="<?php
                            if (!empty($items_info)) {
                                echo $items_info->unit_cost;
                            }
                            ?>" name="unit_cost" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('unit') . ' ' . lang('type') ?></label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" value="<?php
                            if (!empty($items_info)) {
                                echo $items_info->unit_type;
                            }
                            ?>" placeholder="<?= lang('unit_type_example') ?>"
                                   name="unit_type">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('quantity') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <input type="text" data-parsley-type="number" class="form-control" value="<?php
                            if (!empty($items_info)) {
                                echo $items_info->quantity;
                            }
                            ?>" name="quantity" required="">
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('item') . ' ' . lang('group') ?> </label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <select name="customer_group_id" class="form-control select_box">
                                    <option value=""><?= lang('none') ?></option>
                                    <?php
                                    $all_customer_group = $this->db->where('type', 'items')->order_by('customer_group_id','DESC')->get('tbl_customer_group')->result();
                                    if (!empty($all_customer_group)) {
                                        foreach ($all_customer_group as $customer_group) {
                                            ?>
                                            <option value="<?= $customer_group->customer_group_id ?>" <?php
                                            if (!empty($items_info) && $items_info->customer_group_id == $customer_group->customer_group_id) {
                                                echo 'selected';
                                            }
                                            ?>><?= $customer_group->customer_group ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="input-group-addon"
                                     title="<?= lang('new') . ' ' . lang('item') . ' ' . lang('group') ?>"
                                     data-toggle="tooltip" data-placement="top">
                                    <a data-toggle="modal" data-target="#myModal"
                                       href="<?= base_url() ?>admin/items/items_group"><i
                                            class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('tax') ?></label>
                        <div class="col-lg-5">
                            <?php

                            $taxes = $this->db->order_by('tax_rate_percent', 'ASC')->get('tbl_tax_rates')->result();
                            if (!empty($items_info->tax_rates_id) && !is_numeric($items_info->tax_rates_id)) {
                                $tax_rates_id = json_decode($items_info->tax_rates_id);
                            }
                            $select = '<select class="selectpicker" data-width="100%" name="tax_rates_id[]" multiple data-none-selected-text="' . lang('no_tax') . '">';
                            foreach ($taxes as $tax) {
                                $selected = '';
                                if (!empty($tax_rates_id) && is_array($tax_rates_id)) {
                                    if (in_array($tax->tax_rates_id, $tax_rates_id)) {
                                        $selected = ' selected ';
                                    }
                                }
                                $select .= '<option value="' . $tax->tax_rates_id . '"' . $selected . 'data-taxrate="' . $tax->tax_rate_percent . '" data-taxname="' . $tax->tax_rate_name . '" data-subtext="' . $tax->tax_rate_name . '">' . $tax->tax_rate_percent . '%</option>';
                            }
                            $select .= '</select>';
                            echo $select;
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"></label>
                        <div class="col-lg-5">
                            <button type="submit" class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane <?= $active == 3 ? 'active' : ''; ?>" id="group">

                <div class="table-responsive">
                    <table class="table table-striped ">
                        <thead>
                        <tr>
                            <th><?= lang('group') . ' ' . lang('name') ?></th>
                            <th><?= lang('description') ?></th>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <th><?= lang('action') ?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $all_customer_group = $this->db->where('type', 'items')->get('tbl_customer_group')->result();
                        if (!empty($all_customer_group)) {
                            foreach ($all_customer_group as $customer_group) {
                                ?>
                                <tr id="table_items_group_<?= $customer_group->customer_group_id ?>">
                                    <td><?php
                                        $id = $this->uri->segment(5);
                                        if (!empty($id) && $id == $customer_group->customer_group_id) { ?>
                                        <form method="post"
                                              action="<?= base_url() ?>admin/items/saved_group/<?php
                                              if (!empty($group_info)) {
                                                  echo $group_info->customer_group_id;
                                              }
                                              ?>" class="form-horizontal">
                                            <input type="text" name="customer_group" value="<?php
                                            if (!empty($customer_group)) {
                                                echo $customer_group->customer_group;
                                            }
                                            ?>" class="form-control"
                                                   placeholder="<?= lang('enter') . ' ' . lang('group') . ' ' . lang('name') ?>"
                                                   required>
                                        <?php } else {
                                            echo $customer_group->customer_group;
                                        }
                                        ?></td>
                                    <td><?php
                                        $id = $this->uri->segment(5);
                                        if (!empty($id) && $id == $customer_group->customer_group_id) { ?>
                                            <textarea name="description" rows="1" class="form-control"><?php
                                                if (!empty($customer_group)) {
                                                    echo $customer_group->description;
                                                }
                                                ?></textarea>
                                        <?php } else {
                                            echo $customer_group->description;
                                        }
                                        ?></td>
                                    <td>
                                        <?php
                                        $id = $this->uri->segment(5);
                                        if (!empty($id) && $id == $customer_group->customer_group_id) { ?>
                                            <?= btn_update() ?>
                                            </form>
                                            <?= btn_cancel('admin/items/items_list/group/') ?>
                                        <?php } else { ?>
                                        <?= btn_edit('admin/items/items_list/group/' . $customer_group->customer_group_id) ?>
                                        <?php echo ajax_anchor(base_url("admin/items/delete_group/" . $customer_group->customer_group_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_items_group_" . $customer_group->customer_group_id)); ?>
                                    </td>
                                    <?php } ?>
                                </tr>
                            <?php }
                        } ?>
                        <form role="form"
                              enctype="multipart/form-data" id="form"
                              action="<?php echo base_url(); ?>admin/items/saved_group/<?php
                              if (!empty($group_info)) {
                                  echo $group_info->customer_group_id;
                              }
                              ?>" method="post" class="form-horizontal  ">
                            <tr>
                                <td><input required type="text" name="customer_group" class="form-control"
                                           placeholder="<?= lang('enter') . ' ' . lang('group') . ' ' . lang('name') ?>">
                                </td>
                                <td>
                                                    <textarea name="description" rows="1"
                                                              class="form-control"></textarea>
                                </td>
                                <td><?= btn_add() ?></td>
                            </tr>
                        </form>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>