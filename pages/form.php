<?php include('header.php') ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-sm-12">
            <form id="form-create-invoice" class="mt-5 mb-5" method="POST">
                <h2>Create invoice:</h2>
                <?php if (isset($errors) && !empty($errors)): ?>
                    <?php foreach ($errors as $key => $error): ?>
                        <div class="alert alert-danger alert-dismissible fade show"><?php echo $key; ?> : <?php echo $error; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="form-group">
                    <label for="currency">Cryptocurrency:</label>
                    <select name="currency" class="form-control" id="currency" required>
                        <?php foreach ($currencies as $currency): ?>
                            <option value="<?php echo $currency['cid']; ?>"><?php echo $currency['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <div class="row justify-content-space-between">
                        <div class="col-3">
                            <label for="amount">Amount:</label>
                        </div>
                        <div class="col-9 text-right">
                            <div class="form-check form-group">
                                <input class="form-check-input" type="checkbox" name="amount_in_usd" value="true" id="amount_in_usd" on-change="">
                                <label class="form-check-label" for="amount_in_usd">
                                    Amount in USD
                                </label>
                            </div>
                        </div>
                    </div>
                    <input type="text" name="amount" class="form-control" id="amount" required/>
                </div>

                <div class="form-group">
                    <label for="order_number">Order number:</label>
                    <input type="number" name="order_number" class="form-control" id="order_number" required/>
                </div>

                <div class="form-group">
                    <label for="order_name">Order name:</label>
                    <input type="text" name="order_name" class="form-control" id="order_name" required/>
                </div>

                <div class="form-group">
                    <label for="description">Order description:</label>
                    <textarea name="description" class="form-control" id="description"></textarea>
                </div>

                <button type="submit" class="btn btn-primary align-self-end">Create invoice</button>
            </form>
        </div>
    </div>

</div>

<script>
    window.currencies = <?php echo json_encode($currencies); ?>;
    currencies = currencies.reduce((res, cur) => {
        res[cur.cid] = cur;
        return res;
    }, {});
</script>
<script src="/assets/js/create-invoice.js" defer></script>

<?php include('footer.php') ?>
