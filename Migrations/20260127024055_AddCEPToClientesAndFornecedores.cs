using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace CadastroUsuarios.Migrations
{
    /// <inheritdoc />
    public partial class AddCEPToClientesAndFornecedores : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "usuarios",
                columns: table => new
                {
                    id = table.Column<int>(type: "INTEGER", nullable: false)
                        .Annotation("Sqlite:Autoincrement", true),
                    nome = table.Column<string>(type: "TEXT", maxLength: 100, nullable: false),
                    email = table.Column<string>(type: "TEXT", maxLength: 100, nullable: false),
                    senha = table.Column<string>(type: "TEXT", maxLength: 255, nullable: false),
                    datacadastro = table.Column<DateTime>(type: "timestamp with time zone", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_usuarios", x => x.id);
                });

            migrationBuilder.CreateTable(
                name: "clientes",
                columns: table => new
                {
                    id = table.Column<int>(type: "INTEGER", nullable: false)
                        .Annotation("Sqlite:Autoincrement", true),
                    nome = table.Column<string>(type: "TEXT", maxLength: 200, nullable: false),
                    cpf_cnpj = table.Column<string>(type: "TEXT", maxLength: 20, nullable: false),
                    email = table.Column<string>(type: "TEXT", maxLength: 200, nullable: false),
                    telefone = table.Column<string>(type: "TEXT", maxLength: 30, nullable: false),
                    cep = table.Column<string>(type: "TEXT", maxLength: 10, nullable: false),
                    endereco = table.Column<string>(type: "TEXT", maxLength: 300, nullable: false),
                    usuarioid = table.Column<int>(type: "INTEGER", nullable: false),
                    datacadastro = table.Column<DateTime>(type: "timestamp with time zone", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_clientes", x => x.id);
                    table.ForeignKey(
                        name: "FK_clientes_usuarios_usuarioid",
                        column: x => x.usuarioid,
                        principalTable: "usuarios",
                        principalColumn: "id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "fornecedores",
                columns: table => new
                {
                    id = table.Column<int>(type: "INTEGER", nullable: false)
                        .Annotation("Sqlite:Autoincrement", true),
                    nomefantasia = table.Column<string>(type: "TEXT", maxLength: 200, nullable: false),
                    cnpj = table.Column<string>(type: "TEXT", maxLength: 20, nullable: false),
                    emailvendas = table.Column<string>(type: "TEXT", maxLength: 200, nullable: false),
                    telefone = table.Column<string>(type: "TEXT", maxLength: 30, nullable: false),
                    cep = table.Column<string>(type: "TEXT", maxLength: 10, nullable: false),
                    endereco = table.Column<string>(type: "TEXT", maxLength: 500, nullable: false),
                    usuarioid = table.Column<int>(type: "INTEGER", nullable: false),
                    datacadastro = table.Column<DateTime>(type: "timestamp with time zone", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_fornecedores", x => x.id);
                    table.ForeignKey(
                        name: "FK_fornecedores_usuarios_usuarioid",
                        column: x => x.usuarioid,
                        principalTable: "usuarios",
                        principalColumn: "id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "produtos",
                columns: table => new
                {
                    id = table.Column<int>(type: "INTEGER", nullable: false)
                        .Annotation("Sqlite:Autoincrement", true),
                    descricao = table.Column<string>(type: "TEXT", maxLength: 200, nullable: false),
                    quantidade = table.Column<int>(type: "INTEGER", nullable: false),
                    valor = table.Column<decimal>(type: "TEXT", nullable: false),
                    fornecedor = table.Column<string>(type: "TEXT", maxLength: 200, nullable: false),
                    caminhoimagem = table.Column<string>(type: "TEXT", nullable: true),
                    usuarioid = table.Column<int>(type: "INTEGER", nullable: false),
                    datacadastro = table.Column<DateTime>(type: "timestamp with time zone", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_produtos", x => x.id);
                    table.ForeignKey(
                        name: "FK_produtos_usuarios_usuarioid",
                        column: x => x.usuarioid,
                        principalTable: "usuarios",
                        principalColumn: "id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_clientes_usuarioid_nome",
                table: "clientes",
                columns: new[] { "usuarioid", "nome" });

            migrationBuilder.CreateIndex(
                name: "IX_fornecedores_usuarioid_nomefantasia",
                table: "fornecedores",
                columns: new[] { "usuarioid", "nomefantasia" });

            migrationBuilder.CreateIndex(
                name: "IX_produtos_usuarioid",
                table: "produtos",
                column: "usuarioid");

            migrationBuilder.CreateIndex(
                name: "IX_usuarios_email",
                table: "usuarios",
                column: "email",
                unique: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "clientes");

            migrationBuilder.DropTable(
                name: "fornecedores");

            migrationBuilder.DropTable(
                name: "produtos");

            migrationBuilder.DropTable(
                name: "usuarios");
        }
    }
}
