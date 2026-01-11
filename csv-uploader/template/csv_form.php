<style>
.my-csv-form {
    max-width: 400px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-family: Arial, sans-serif;
}
</style>

<div class="my-csv-form">
    <p id="show_upload_message"></p>
    <h1>Form Data</h1>

    <form class="form-csv-upload" method="post" enctype="multipart/form-data">
        <p>
            <label for="csv_data_file">Upload CSV File</label>
            <input type="file" name="csv_data_file" id="csv_data_file" required>
        </p>

        <p>
            <button type="submit">Upload CSV File</button>
        </p>
    </form>
</div>
