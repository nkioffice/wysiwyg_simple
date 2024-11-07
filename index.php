<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>エディタ</title>
</head>

<body>
    <section class="buttons">
        <div class="f">
            <button id="h1">H1</button>
            <button id="h2">H2</button>
            <button id="h3">H3</button>
            <button onclick="insertLink()">LINK</button>
            <button class="color" id="color">COLOR</button>
            <input type="file" name="image" accept="image/*" id="image-up">
        </div>
        <div class="f">
            <button id="save">下書き保存</button>
        </div>

    </section>
    <div class="colors" style="display: none;">
        <button class="color-button" style="background-color: red;" onclick="changeColor('red')"></button>
        <button class="color-button" style="background-color: blue;" onclick="changeColor('blue')"></button>
        <button class="color-button" style="background-color: green;" onclick="changeColor('green')"></button>
        <button class="color-button" style="background-color: orange;" onclick="changeColor('orange')"></button>
        <button class="color-button" style="background-color: purple;" onclick="changeColor('purple')"></button>

    </div>
    <div>
        <section class="index">
            <h4>目次</h4>
            <ol id="index">

            </ol>

        </section>
        <section class="edit-area">
            <div class="wrapper">
                <div contenteditable="true" class="txt">
                </div>
                <button class="del-button">delete</button>
            </div>
        </section>
    </div>

</body>
<script>
    //初期化
    let focusElem = null;
    let focusWrapper = null;
    const wrapper = document.createElement('div');
    wrapper.className = 'wrapper';
    const txtArea = document.createElement('div');
    txtArea.contentEditable = 'true';
    txtArea.className = 'txt';
    const delButton = document.createElement('button');
    delButton.className = 'del-button';

    const editArea = document.querySelector('.edit-area')

    let undoStack = [editArea.innerHTML];
    //undo
    editArea.addEventListener('input', function() {
        undoStack.push(this.innerHTML);
    })
    document.addEventListener('mousedown', function() {
        undoStack.push(editArea.innerHTML);
    })
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key == "z") {
            let undo = undoStack.pop();
            editArea.innerHTML = undo;
            onInit(false)
        }
    })

    //各ボタン
    const btnH1 = document.getElementById('h1');
    const btnH2 = document.getElementById('h2');
    const btnH3 = document.getElementById('h3');
    const btnImage = document.getElementById('image-up')
    const btnDraft = document.getElementById('save');

    onInit(true);


    function onInit(firstTime) {
        if (firstTime) {
            focusElem = document.querySelector('.txt');
            focusWrapper = focusElem.parentElement;
            focusElem.focus();

            //Enterで新しいNodeを追加する
            document.addEventListener('keydown', function(e) {
                console.log(e.key);
                if (!e.shiftKey && e.key === "Enter") {
                    e.preventDefault();
                    addDiv();
                }
            })
            //focusの監視
            document.addEventListener('mousedown', function(e) {
                if (e.target.className === 'txt') {
                    focusElem = e.target;
                    focusWrapper = focusElem.parentElement;
                }
            })
        }
        //削除ボタン
        document.querySelectorAll('.del-button').forEach(element => {
            element.addEventListener('click', function() {
                removeParentNode(element);
            })
        })
        if (firstTime) {
            //見出しボタン
            btnH1.addEventListener('click', function(e) {
                let innerHTML = focusElem.innerHTML;

                if (innerHTML.startsWith('<h1') || innerHTML.startsWith('<h2') || innerHTML.startsWith('<h3')) {
                    innerHTML = innerHTML.replace(/<h[1-3]/g, '<h1');
                    innerHTML = innerHTML.replace(/<\/h[1-3]>/g, '</h1>');
                } else {
                    innerHTML = `<h1>${innerHTML}</h1>`;
                }

                focusElem.innerHTML = innerHTML;
                refreshIndex();
            });
            btnH2.addEventListener('click', function(e) {
                let innerHTML = focusElem.innerHTML;

                if (innerHTML.startsWith('<h1') || innerHTML.startsWith('<h2') || innerHTML.startsWith('<h3')) {
                    innerHTML = innerHTML.replace(/<h[1-3]/g, '<h2');
                    innerHTML = innerHTML.replace(/<\/h[1-3]>/g, '</h2>');
                } else {
                    innerHTML = `<h2>${innerHTML}</h2>`;
                }

                focusElem.innerHTML = innerHTML;
                refreshIndex();
            });
            btnH3.addEventListener('click', function(e) {
                let innerHTML = focusElem.innerHTML;

                if (innerHTML.startsWith('<h1') || innerHTML.startsWith('<h2') || innerHTML.startsWith('<h3')) {
                    innerHTML = innerHTML.replace(/<h[1-3]/g, '<h3');
                    innerHTML = innerHTML.replace(/<\/h[1-3]>/g, '</h3>');
                } else {
                    innerHTML = `<h3>${innerHTML}</h3>`;
                }

                focusElem.innerHTML = innerHTML;
                refreshIndex();

            })
            //色
            document.getElementById('color').addEventListener('click', function(e) {
                document.querySelector('.colors').style.display = 'flex';
            })
            //画像
            //画像を挿入
            btnImage.addEventListener("change", function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();

                    // 画像をサーバーに送信
                    const formData = new FormData();
                    formData.append('file', file);

                    fetch('upload_image.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(result => {
                            console.log(result);
                            if (result.success) {
                                let elem = addDiv();
                                let srcName = result.fileUrl;
                                let alt = prompt('altを入力');
                                let imgTag = `<img src = "${srcName}" alt="${alt}">`;
                                elem.querySelector('.txt').innerHTML = imgTag;

                            } else {
                                console.log(result.message);
                                alert('php.iniでアップロード可能なファイルサイズを確認してください');
                            }

                        })
                        .catch(error => {
                            console.error('エラー:', error);
                        });


                }
            })
            //下書き保存
            btnDraft.addEventListener('click', function() {
                saveDraft();
            })
        }

    }

    function onAddDiv() {
        document.querySelectorAll('[contenteditable="true"]').forEach(element => {
            element.addEventListener('focus', () => {
                focusElem = element;
                focusWrapper = focusElem.parentElement;
                console.log("focus", focusWrapper);
            });
        })
    }

    /**要素を追加する
     * 戻り値：追加した要素
     * 
     */
    function addDiv() {
        let newElement = wrapper.cloneNode();
        let newTxt = txtArea.cloneNode();
        let newBtn = delButton.cloneNode();
        newBtn.textContent = 'delete'
        newBtn.addEventListener('click', function() {
            removeParentNode(newBtn);
        })
        newElement.appendChild(newTxt);
        newElement.appendChild(newBtn)
        console.log(focusWrapper);

        focusWrapper.insertAdjacentElement('afterend', newElement);
        console.log('newElement', newElement);
        //focus()ではeventが呼ばれない
        focusElem = newTxt;
        focusWrapper = newElement;
        newTxt.focus();
        onAddDiv();

        return newElement;
    }

    function removeParentNode(elem) {
        elem.parentElement.remove();
    }
    /**@function
     * UNDO用
     */
    function saveHtml() {

    }

    function changeColor(className) {
        // 現在の選択範囲を取得
        const selection = window.getSelection();

        // 選択範囲が存在し、範囲が空でない場合に処理を実行
        if (selection && !selection.isCollapsed) {
            const range = selection.getRangeAt(0);
            const selectedText = range.extractContents();

            const span = document.createElement("span");
            span.classList.add(className);
            span.appendChild(selectedText);

            // 選択範囲の開始位置に赤い <span> 要素を挿入
            range.insertNode(span);

            // 選択解除
            selection.removeAllRanges();
        }
    }

    function insertLink() { // 現在の選択範囲を取得
        const selection = window.getSelection();

        // 選択範囲が存在し、範囲が空でない場合に処理を実行
        if (selection && !selection.isCollapsed) {
            const range = selection.getRangeAt(0);
            const selectedText = range.extractContents();


            const aTag = document.createElement("a");

            aTag.href = prompt('URLを入力してください', 'https://');
            aTag.appendChild(selectedText);

            range.insertNode(aTag);

            // 選択解除
            selection.removeAllRanges();
        }

    }


    function saveDraft() {
        const formData = new FormData();
        let html = editArea.innerHTML;
        let title = prompt('タイトルを入力してください', 'タイトル：')
        formData.append('title', title);
        formData.append('html', html);

        fetch('save_draft.php', {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                console.log(result.num);
                draft_number = result.num;
                if (result.success) {
                    alert('下書きを保存しました！');
                }
            })
            .catch(err => {
                console.log(err);
                alert('save_draft.phpをデータベースに接続してください')
            });
    }

    function refreshIndex() {
        const indexList = document.getElementById('index');
        indexList.innerHTML = ''; // 既存の目次をクリア

        // editArea内の h1, h2, h3 をすべて取得
        const headings = editArea.querySelectorAll('h1, h2, h3');
        let idCounter = 1;

        headings.forEach(heading => {
            // headingにユニークなIDを付与
            const id = `heading-${idCounter++}`;
            heading.id = id;

            // <li><a href="#heading-x">見出しのテキスト</a></li> を作成
            const listItem = document.createElement('li');
            const link = document.createElement('a');
            link.href = `#${id}`;
            link.textContent = heading.textContent;

            listItem.appendChild(link);
            indexList.appendChild(listItem);
        });
    }
</script>

</html>