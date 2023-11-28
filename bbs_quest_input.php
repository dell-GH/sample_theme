<?php
/*
Template Name: bbs_quest_input
固定ページ: 質問する
*/
header('X-FRAME-OPTIONS: SAMEORIGIN');
get_header();
get_header('menu');
?>
<h1>質問する</h1>
<div id="input_area">
    <h2>入力画面</h2>
    <form name="input_form">
        <div>
            <h2>名前</h2>
            <div class="parts">
                <input class="input" data-length="<?php echo MAX_LENGTH::NAME; ?>" type="text" name="namae" id="name">
                <div></div>
            </div>
        </div>
        <div>
            <h2>コメント</h2>
            <div class="parts">
                <textarea class="input" data-length="<?php echo MAX_LENGTH::MESSAGE; ?>" name="message" id="message"></textarea>
                <div></div>
            </div>
        </div>
        <div>
            <input type="radio" name="stamp" value="1" id="stamp_1"><label for="stamp_1"></label>
            <input type="radio" name="stamp" value="2" id="stamp_2"><label for="stamp_2"></label>
            <input type="radio" name="stamp" value="3" id="stamp_3"><label for="stamp_3"></label>
            <input type="radio" name="stamp" value="4" id="stamp_4"><label for="stamp_4"></label>
            <input type="radio" name="stamp" value="5" id="stamp_5"><label for="stamp_5"></label>
            <input type="radio" name="stamp" value="6" id="stamp_6"><label for="stamp_6"></label>
            <input type="radio" name="stamp" value="7" id="stamp_7"><label for="stamp_7"></label>
            <input type="radio" name="stamp" value="8" id="stamp_8"><label for="stamp_8"></label>
        </div>
        <div>
            <input type="file" class="attach" name="attach[]">
            <div class="viewer"></div>
        </div>
        <div>
            <input type="file" class="attach" name="attach[]">
            <div class="viewer"></div>
        </div>
        <div>
            <input type="file" class="attach" name="attach[]">
            <div class="viewer"></div>
        </div>
        <div>
            <button type="button" id="input_button">確認画面へ進む</button>
        </div>
    </form>
</div>
<div id="confirm_area"></div>
<div id="result_area"></div>
<script>
    function validation_submit(f) {
        const submit = document.getElementById("input_button");
        /* 判定は逆なので、逆に渡す */
        submit.disabled = f ? false : true;
    };
    function validation_text(parts) {
        /* このpartsグループの、inputを抽出 */
        let text = parts.getElementsByClassName('input')[0];
        /* 最小チェック */
        if (text.value.length == 0) {
            return false;
        }
        /* 最大チェック */
        if (text.value.length >= text.dataset.length) {
            return false;
        }
        return true;
    };
    /* バリデーション条件判断部分 */
    function validation() {
        let parts = document.getElementsByClassName('parts');
        let submit = true;
        for (let i = 0; i < parts.length; i++) {
            if (validation_text(parts[i]) != true) {
                submit = false;
            }
        }
        validation_submit(submit);
    };
    const input_area = document.getElementById("input_area");
    const confirm_area = document.getElementById("confirm_area");
    const result_area = document.getElementById("result_area");
    var namae_value = "";
    var message_value = "";
    var stamp_value = "";
    // 要素が3個の空配列を作成
    const blobType = new Array(3);
    const blobUrl = new Array(3);
    const init = function() {
        set_attach_event();
        document.getElementById("input_button").addEventListener("click", input_button_click);
        /* 文字数表示 */
        document.addEventListener('input', e => {
            if (!['name', 'message'].includes(e.target.id)) return;
            const
                t = e.target,
                m = t.nextElementSibling,
                n = t.value.length - (t.dataset.length | 0),
                c = document.createElement('span');
            c.append(Math.abs(n));
            m.style.color = n > 0 ? 'red' : 'black';
            m.replaceChildren(n > 0 ? '' : '残り', c,
                `文字${n > 0 ? '超過してい' : '入力でき'}ます。`);
            /* 毎回判定によるボタン制御 */
            validation();
        });
        /* 初回判定のボタン制御 */
        validation();
    }
    //DOM構築、スタイルシート、画像、サブフレームの読み込みが完了した後に発生する
    window.addEventListener("DOMContentLoaded", init);
    const set_attach_event = function() {
        const attach = document.querySelectorAll(".attach");
        const viewer = document.querySelectorAll(".viewer");
        for (let i = 0; i < attach.length; i++) {
            attach[i].addEventListener("change", () => {
                //HTML要素の中身を変更するときに使われるプロパティ
                viewer[i].innerHTML = "";
                blobType[i] = "";
                blobUrl[i] = "";
                if (attach[i].files.length !== 0) {
                    //オブジェクトのURLを作成する
                    blobUrl[i] = window.URL.createObjectURL(attach[i].files[0]);
                    //ファイルの内容を読み込む FileReaderオブジェクト を生成し、ファイルの内容を非同期で取得
                    const reader = new FileReader();
                    reader.onload = () => {
                        var child = null;
                        //result プロパティは、ファイルの内容を返す
                        if (reader.result.indexOf("data:image/jpeg;base64,") === 0 ||
                            reader.result.indexOf("data:image/png;base64,") === 0) {
                            blobType[i] = "img";
                            child = document.createElement("img");
                        } else if (reader.result.indexOf("data:video/mp4;base64,") === 0) {
                            blobType[i] = "video";
                            child = document.createElement("video");
                            child.setAttribute("controls", null);
                        } else if (reader.result.indexOf("data:application/pdf;base64,") === 0) {
                            blobType[i] = "iframe";
                            child = document.createElement("iframe");
                        } else {
                            alert("対象外のファイルです");
                            attach[i].value = "";
                        }
                        if (child !== null) {
                            child.style.maxHeight = "200px";
                            child.style.maxWidth = "200px";
                            child.src = blobUrl[i];
                            //戻り値は追加した子要素 viewer[i]
                            viewer[i].appendChild(child);
                        }
                    };
                    //指定されたBlob または File の内容を読み込む
                    reader.readAsDataURL(attach[i].files[0]);
                }
            });
        };
    }
    const input_button_click = function() {
        namae_value = "";
        message_value = "";
        stamp_value = "";
        //サーバーにデータを送信する際に使用するオブジェクトを生成
        const formData = new FormData(input_form);
        //オブジェクト内の既存のキーに新しい値を追加
        formData.append("action", "bbs_quest_input");
        const opt = {
            method: "post",
            body: formData
        }
        //非同期通信
        fetch("<?php echo home_url('wp-admin/admin-ajax.php'); ?>", opt)
            .then(response => {
                return response.json();
            })
            .then(json => {
                if (json.error != "") {
                    alert(json.error);
                    return;
                }
                namae_value = json.namae;
                message_value = json.message;
                const stamps = document.getElementsByName('stamp');
                for (var stamp of stamps) {
                    //checkedプロパティは、対象の要素がcheckedを持っていればtrueを、持っていなければfalseを返す
                    if (stamp.checked) {
                        stamp_value = stamp.value;
                        break;
                    }
                }
                // 空文字を入れることで要素内を空にできる
                confirm_area.textContent = '';
                var div;
                var child;
                child = document.createElement("h2");
                child.appendChild(document.createTextNode('確認画面'));
                confirm_area.appendChild(child); // confirm_area の末尾に child を追加
                div = document.createElement("div");
                child = document.createElement("p");
                child.appendChild(document.createTextNode("名前：" + namae_value));
                div.appendChild(child); // div の末尾に child を追加
                confirm_area.appendChild(div); // confirm_area の末尾に div を追加
                div = document.createElement("div");
                child = document.createElement("p");
                child.appendChild(document.createTextNode("コメント：" + message_value));
                div.appendChild(child); // div の末尾に child を追加
                confirm_area.appendChild(div); // confirm_area の末尾に div を追加
                div = document.createElement("div");
                child = document.createElement("input");
                child.type = "radio";
                child.name = "stamp";
                child.id = "confirm_stamp";
                child.value = stamp_value;
                child.checked = true;
                div.appendChild(child); // div の末尾に child を追加
                child = document.createElement("label");
                child.htmlFor = "confirm_stamp";
                div.appendChild(child); // div の末尾に child を追加
                confirm_area.appendChild(div); // confirm_area の末尾に div を追加
                div = document.createElement("div");
                for (const i in blobType) {
                    if (blobType[i] != "") {
                        child = null;
                        if (blobType[i] == "img") {
                            child = document.createElement("img");
                        } else if (blobType[i] == "video") {
                            child = document.createElement("video");
                            child.setAttribute("controls", null);
                        } else if (blobType[i] == "iframe") {
                            child = document.createElement("iframe");
                        }
                        if (child !== null) {
                            child.style.maxHeight = "200px";
                            child.style.maxWidth = "200px";
                            child.src = blobUrl[i];
                            div.appendChild(child);
                        }
                    }
                }
                confirm_area.appendChild(div); // confirm_area の末尾に div を追加
                div = document.createElement("div");
                child = document.createElement("button");
                child.type = "button";
                child.innerText = "入力画面へ戻る";
                child.addEventListener("click", () => {
                    input_area.style.display = "block";
                    // 空文字を入れることで要素内を空にできる
                    confirm_area.textContent = '';
                    confirm_area.style.display = "none";
                });
                div.appendChild(child); // div の末尾に child を追加
                child = document.createElement("button");
                child.type = "button";
                //name属性の追加・変更
                child.setAttribute("name", "sample1");
                child.innerText = "結果画面へ進む";
                child.addEventListener("click", confirm_button_click);
                div.appendChild(child); // div の末尾に child を追加
                confirm_area.appendChild(div); // confirm_area の末尾に div を追加
                input_area.style.display = "none";
                confirm_area.style.display = "block";
            })
            .catch(error => {});
    }
    const confirm_button_click = function() {
        const formData = new FormData();
        formData.append("action", "bbs_quest_confirm");
        const opt = {
            method: "post",
            body: formData
        }
        fetch("<?php echo home_url('wp-admin/admin-ajax.php'); ?>", opt)
            .then(response => {
                return response.json();
            })
            .then(json => {
                if (json.error != "") {
                    alert(json.error);
                    return;
                }
                // 空文字を入れることで要素内を空にできる
                result_area.textContent = '';
                var div;
                var child;
                child = document.createElement("h2");
                child.appendChild(document.createTextNode('結果画面'));
                result_area.appendChild(child); // result_area の末尾に child を追加
                div = document.createElement("div");
                child = document.createElement("p");
                child.appendChild(document.createTextNode("名前：" + namae_value));
                div.appendChild(child); // div の末尾に child を追加
                result_area.appendChild(div); // result_area の末尾に div を追加
                div = document.createElement("div");
                child = document.createElement("p");
                child.appendChild(document.createTextNode("コメント：" + message_value));
                div.appendChild(child); // div の末尾に child を追加
                result_area.appendChild(div); // result_area の末尾に div を追加
                div = document.createElement("div");
                child = document.createElement("input");
                child.type = "radio";
                child.name = "stamp";
                child.id = "result_stamp";
                child.value = stamp_value;
                child.checked = true;
                div.appendChild(child); // div の末尾に child を追加
                child = document.createElement("label");
                child.htmlFor = "result_stamp";
                div.appendChild(child); // div の末尾に child を追加
                result_area.appendChild(div); // result_area の末尾に div を追加
                div = document.createElement("div");
                for (const i in blobType) {
                    if (blobType[i] != "") {
                        child = null;
                        if (blobType[i] == "img") {
                            child = document.createElement("img");
                        } else if (blobType[i] == "video") {
                            child = document.createElement("video");
                            child.setAttribute("controls", null);
                        } else if (blobType[i] == "iframe") {
                            child = document.createElement("iframe");
                        }
                        if (child !== null) {
                            child.style.maxHeight = "200px";
                            child.style.maxWidth = "200px";
                            child.src = blobUrl[i];
                            div.appendChild(child); // div の末尾に child を追加
                        }
                    }
                }
                result_area.appendChild(div); // result_area の末尾に div を追加
                confirm_area.style.display = "none";
            })
            .catch(error => {});
    }
</script>