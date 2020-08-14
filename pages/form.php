<?php include('header.php') ?>
    <div class="row">
        <div class="col-sm-8 col-offset-2">
            <form class="form-inline">
                <div class="form-group">
                    <label for="currency">Cryptocurrency:</label>
                    <select name="currency" class="form-control" id="currency">
                        <?php foreach ($currencies as $currency): ?>
                            <option value="<?php echo $currency['cid']; ?>"><?php echo $currency['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
<?php include('footer.php') ?>