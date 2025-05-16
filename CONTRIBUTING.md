---

## 🇬🇧 English

<details open>
<summary>🛠 Contribution Guide</summary>

### 📁 Branch Structure and Contribution Rules

The project uses a branching strategy based on Semantic Versioning.

#### 🔹 `main`

* The main branch for developing the next version.
* All commits and Pull Requests must target it.

#### 🔹 `pre-release/vX.X.X`

* Contains a pre-release version of the corresponding release.
* Before a new release is published, a `release/vX.X.X` branch is created from it.
* Accepts changes **only** from the `main` branch via Pull Requests.

#### 🔹 `release/vX.X.X`

* Represents a finalized release version.
* **Does not accept** new commits or Pull Requests.

#### 🔹 `develop/vX.X.X`

* A branch for maintaining one of the released LTS versions.
* Only changes related to the corresponding LTS release should be merged.
* All changes must go through Pull Requests — direct commits are prohibited.

### 📌 Getting Started

1. **Fork the repository**: Create a copy of the project under your GitHub account.

2. **Create a new branch**: Start from the `main` branch with a descriptive name, e.g., `feature-new-feature` or `bugfix-fix-issue`.

3. **Make changes**: Modify the code or documentation as needed.

4. **Write a commit**: Follow the Conventional Commits format:

   ```
   <type>[optional scope]: <short description>

   [optional body]

   [optional footer]
   ```

   **Examples:**

   * `feat(parser): add array support`
   * `fix(auth): fix token validation bug`
   * `docs(readme): update installation guide`

   **Common commit types:**

   * `feat`: new feature
   * `fix`: bug fix
   * `docs`: documentation only
   * `style`: code style (formatting, spacing, etc.)
   * `refactor`: code change that neither fixes a bug nor adds a feature
   * `perf`: performance improvement
   * `test`: adding or updating tests
   * `build`: changes that affect the build system or dependencies
   * `ci`: CI/CD configuration
   * `chore`: other changes that don't affect source or tests
   * `revert`: revert a previous commit

   **Breaking change example:**

   ```
   feat!: drop support for deprecated API

   BREAKING CHANGE: method `getData()` is no longer supported; use `fetchData()` instead.
   ```

5. **Push the branch**: Upload your branch to your fork on GitHub.

6. **Open a Pull Request**: Create a PR from your branch into the `main` branch of the original repository.

### ✅ Recommendations

* **Code Style**: Follow the project's code style conventions.
* **Testing**: Ensure your code passes all tests and introduces no errors.
* **Documentation**: Update or add docs if your changes affect project behavior.
* **Discussion**: If unsure, open an issue for discussion before starting.

### 📚 Additional Resources

* [Semantic Versioning 2.0.0](https://semver.org/spec/v2.0.0.html)
* [Conventional Commits 1.0.0](https://www.conventionalcommits.org/v1.0.0/)
* [Code Style Guide](https://github.com/EngineGPDev/EngineGP/blob/main/CODE_STYLE.md)

### 📄 License

By submitting changes, you agree that your contribution will be licensed under the project's license.

---

If you have questions or need help, don’t hesitate to open an Issue or Pull Request.

</details>

---

## 🇷🇺 Русский

<details open>
<summary>🛠 Руководство по участию в проекте</summary>

### 📁 Структура веток и правила внесения изменений

Проект использует стратегию ветвления, основанную на семантическом версионировании.

#### 🔹 `main`

* Основная ветка для разработки новой версии.
* Все коммиты и Pull Request должны направляться в неё.

#### 🔹 `pre-release/vX.X.X`

* Содержит предварительную версию соответствующего релиза.
* Перед выпуском новой версии от неё создаётся ветка `release/vX.X.X`.
* Принимает изменения **только** из ветки `main` посредством Pull Request.

#### 🔹 `release/vX.X.X`

* Представляет зафиксированную версию релиза.
* **Не принимает** новых коммитов или Pull Request.

#### 🔹 `develop/vX.X.X`

* Ветка для поддержки одной из выпущенных LTS-версий.
* В неё должны попадать только изменения, относящиеся к соответствующему LTS-выпуску.
* Все изменения должны вноситься исключительно через Pull Request — прямые коммиты запрещены.

### 📌 Как начать

1. **Форкните репозиторий**: Создайте копию проекта в своём аккаунте GitHub.

2. **Создайте новую ветку**: От ветки `main` создайте ветку с описательным названием, например, `feature-новая-функция` или `bugfix-исправление-ошибки`.

3. **Внесите изменения**: Выполните необходимые изменения в коде или документации.

4. **Оформите коммит**: Соблюдайте формат сообщений коммитов согласно спецификации Conventional Commits:

   ```
   <тип>[опциональная область]: <краткое описание>

   [опциональное тело]

   [опциональный подвал]
   ```

   **Примеры:**

   * `feat(parser): добавлена поддержка массивов`
   * `fix(auth): исправлена ошибка валидации токена`
   * `docs(readme): обновлены инструкции по установке`

   **Основные типы коммитов:**

   * `feat`: добавление новой функциональности
   * `fix`: исправление ошибок
   * `docs`: изменения в документации
   * `style`: изменения, не влияющие на смысл кода
   * `refactor`: рефакторинг без добавления функциональности
   * `perf`: улучшение производительности
   * `test`: добавление или изменение тестов
   * `build`: изменения, влияющие на систему сборки или зависимости
   * `ci`: изменения в настройке CI/CD
   * `chore`: прочие изменения
   * `revert`: откат предыдущих изменений

   **Пример коммита с нарушением обратной совместимости:**

   ```
   feat!: удалена поддержка устаревшего API

   BREAKING CHANGE: метод `getData()` больше не поддерживается; используйте `fetchData()` вместо него.
   ```

5. **Запушьте ветку**: Отправьте вашу ветку в форк.

6. **Создайте Pull Request**: Откройте Pull Request в ветку `main` оригинального репозитория.

### ✅ Рекомендации

* **Кодстайл**: Соблюдайте стандарты кодирования проекта.
* **Тестирование**: Убедитесь, что код проходит тесты и не вызывает ошибок.
* **Документация**: Обновите документацию, если поведение проекта изменилось.
* **Обсуждение**: При сомнениях начните обсуждение в Issues перед работой.

### 📚 Дополнительные ресурсы

* [Семантическое версионирование (SemVer) 2.0.0](https://semver.org/spec/v2.0.0.html)
* [Соглашение о коммитах (Conventional Commits) 1.0.0](https://www.conventionalcommits.org/v1.0.0/)
* [Руководство по стилю кода](https://github.com/EngineGPDev/EngineGP/blob/main/CODE_STYLE.md)

### 📄 Лицензия

Отправляя изменения, вы соглашаетесь с тем, что ваш вклад будет распространяться под лицензией проекта.

---

Если у вас возникнут вопросы или потребуется помощь, не стесняйтесь обращаться через Issues или Pull Requests.

</details>

---
