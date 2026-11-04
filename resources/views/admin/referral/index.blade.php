@extends('layouts.app')
@section('content')
    <form action="{{ route('admin.referral.save') }}" method="POST" class="form-horizontal">
        @csrf
        <div class="panel tab-pane" id="referral">
            <div class="row referral-container">
                <div class="col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">
                                Referral Voucher
                                <label class="switch">
                                    <input name="referral_enabled" value="1" type="checkbox" {{ $referralEnabled ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group d-flex align-items-center">
                                <label for="" class="control-label col-sm-3 pt-0">Recipient Enabled</label>
                                <div class="col-sm-9">
                                    <label class="switch">
                                        <input name="referral_recipient_enabled" value="1" type="checkbox" {{ $recipientEnabled ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group" id="recipient_voucher_form_group" {{ $recipientEnabled ? '' : 'style=display:none' }}>
                                <label class="control-label col-sm-3" for="referral_coupon">Recipient voucher</label>
                                <div class="col-sm-9">
                                    <select class="form-control demo-select2-placeholder" data-placeholder="Select Coupon" name=referral_coupon_id id="referral_coupon">
                                        @foreach($coupons as $coupon)
                                            <option {{ $referralCouponId == $coupon->id ? 'selected' : ''}} value="{{ $coupon->id }}">{{ $coupon->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label for="" class="control-label col-sm-3 pt-0">Sender Enabled</label>
                                <div class="col-sm-9">
                                    <label class="switch">
                                        <input name="referral_sender_enabled" value="1" type="checkbox" {{ $senderEnabled ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group" id="sender_voucher_form_group" {{ $recipientEnabled ? '' : 'style=display:none' }}>
                                <label class="control-label col-sm-3" for="referrer_coupon">Sender voucher</label>
                                <div class="col-sm-9">
                                    <select class="form-control demo-select2-placeholder" data-placeholder="Select Coupon" name=referrer_coupon_id id="referrer_coupon">
                                        @foreach($coupons as $coupon)
                                            <option {{ $referrerCouponId == $coupon->id ? 'selected' : ''}} value="{{ $coupon->id }}">{{ $coupon->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group exclude-domain">
                                <label class="control-label col-sm-3" for="exclude_domains">Exclude</label>
                                <div class="col-sm-9 form-input">
                                    <input type="file" id="exclude_domains_file">
                                    <input type="hidden" name="referral_exclude_domains" value="{{ $referralExclDomains }}">
                                    <div>
                                        <button type="button" class="btn btn-default import-domains">Import</button>
                                        <button type="button" class="btn btn-default export-domains">Export</button>
                                    </div>
                                </div>
                                <br />
                                <br />
                                <div class="col-md-12">
                                    <table class="table table-bordered" id="table-exclude-domains">
                                        <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Domain</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($referralExclDomainArr as $key => $domain)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $domain }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">
                                Corporate Access Referral
                                <label class="switch">
                                    <input name="corporate_ref_enabled" value="1" type="checkbox" {{ $corporateRefEnabled ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="corporate_ref_domain">Domain</label>
                                <div class="col-sm-10">
                                    <select class="form-control demo-select2-placeholder" data-placeholder="Select Domain" name="corporate_ref_domain" id="corporate_ref_domain">
                                        @foreach ($corporateDomains as $domain)
                                            <option value="{{ $domain->domain }}">{{ $domain->domain }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="corporate_ref_share_limit">Share limit</label>
                                <div class="col-sm-10">
                                    <input type="number" name="corporate_ref_share_limit" id="corporate_ref_share_limit" class="form-control" value="5">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <button style="width: 100%" type="button" class="btn btn-dark pull-right" id="btn_generate_link">Apply</button>
                                </div>
                            </div>
                            <div class="row">
                                <table class="table table-bordered" id="table_corporate_refs">
                                    <thead>
                                    <tr>
                                        <th class="col-md-3">Domain</th>
                                        <th class="col-md-3">Share Limit</th>
                                        <th class="col-md-3">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($corporateRefs as $corporateRef)
                                            <tr id="{{ str_replace('.', '_', str_replace('@', '', $corporateRef->domain)) }}">
                                                <td>{{ $corporateRef->domain }}</td>
                                                <td>{{ $corporateRef->share_limit }}</td>
                                                <td>
                                                    <a href="{{ route('admin.referral.domain_reset', ['domain' => $corporateRef->domain]) }}" title="Refresh Access Pass">
                                                        <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                                                    </a>
                                                    <a href="{{ route('admin.referral.domain_users', ['domain' => $corporateRef->domain]) }}" title="User list">
                                                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="corporate_refs" id="corporate_refs" value="{{ json_encode($corporateRefs) }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer text-right">
                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
            </div>
        </div>
    </form>

    <script>
        $(function () {
            var corporateRefs = @json($corporateRefs);
            $(".import-domains").click(function () {
                var fileContainer = $('#exclude_domains_file');
                var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.csv)$/;
                var table = $('#table-exclude-domains');
                table.find('tbody').html('');

                var domains = [];
                if (!regex.test(fileContainer.val().toLowerCase())) {
                    alert("Please upload a valid CSV file.");
                    return domains;
                }

                if (typeof (FileReader) === "undefined") {
                    alert("This browser does not support HTML5.");
                    return domains;
                }

                var reader = new FileReader();
                reader.onload = function (e) {
                    var rows = e.target.result.split("\n");
                    for (var i = 0; i < rows.length; i++) {
                        var cells = rows[i].split(",");
                        for (var key in cells) {
                            if (!cells[key]) {
                                continue;
                            }

                            domains.push(cells[key].trim());

                            var newRow = $("<tr></tr>");
                            var newLine = $("<td></td>");
                            newLine.html(parseInt(i) + 1);
                            newRow.append(newLine);
                            newLine = $("<td></td>");
                            newLine.html(cells[key]);
                            newRow.append(newLine);

                            table.find('tbody').append(newRow);
                        }

                        $('input:hidden[name="referral_exclude_domains"]').val(JSON.stringify(domains));
                    }
                }

                reader.readAsText(fileContainer[0].files[0]);
            });

            $('body').on('change', 'input[name="referral_recipient_enabled"]', function () {
                console.log($(this).prop('checked'));
                var checked = $(this).prop('checked');
                if (checked) {
                    $('#recipient_voucher_form_group').show();
                } else {
                    $('#recipient_voucher_form_group').hide();
                }
            });

            $('body').on('change', 'input[name="referral_sender_enabled"]', function () {
                var checked = $(this).prop('checked');
                if (checked) {
                    $('#sender_voucher_form_group').show();
                } else {
                    $('#sender_voucher_form_group').hide();
                }
            });

            function copyToClipboard(copySource, target) {
                copySource.select();
                copySource.setSelectionRange(0, 99999)
                document.execCommand("copy");

                target.attr('title', 'Copied');
                target.tooltip();
                target.tooltip('show');
            }

            $('body').on('click', '.copy-clipboard', function (e) {
               var row = $(e.target).closest('tr');
               var linkEl = row.find('.link-col').find('input');
               console.log(linkEl.get(0));
               copyToClipboard(linkEl.get(0), $(e.target))
            });

            $('#btn_generate_link').click(function () {
                var domain = $('#corporate_ref_domain').val();

                var corporateRefObj = {
                    domain: domain,
                    share_limit: $('#corporate_ref_share_limit').val(),
                };

                var refsTable = $('#table_corporate_refs');
                var newRow = $('<tr></tr>');
                var existedRow = refsTable.find('#' + domain.replace('@', '').replace(/\./g, '_'));
                if (existedRow.length > 0) {
                    existedRow.html('');
                    newRow = existedRow
                }

                for (var key in corporateRefObj) {
                    newRow.attr('id', domain.replace('@', '').replace(/\./g, '_'));
                    newCell = $('<td></td>');
                    newCell.html(corporateRefObj[key]);
                    newRow.append(newCell);
                }

                if (existedRow.length <= 0) {
                    refsTable.find('tbody').append(newRow);
                    corporateRefs.push(corporateRefObj);
                } else {
                    for (var index in corporateRefs) {
                        if (corporateRefs[index].domain !== corporateRefObj.domain) {
                            continue;
                        }
                        corporateRefs[index] = corporateRefObj;
                    }
                }

                $('#corporate_refs').val(JSON.stringify(corporateRefs));
            })
        });
    </script>
@endsection

